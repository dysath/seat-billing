@extends('web::layouts.grids.12')

@section('title', trans('billing::billing.pastbill'))
@section('page_header', trans('billing::billing.pastbill'))

@section('full')
  <input type="hidden" id="year" value="{{ $year }}">
  <input type="hidden" id="month" value="{{ $month }}">

  <div class="box box-default box-solid">
    <div class="box-header with-border">
      <h4>{{ trans('billing::billing.previousbill') }}</h4>
    </div>
    <div class="box-body">
      @foreach($dates->chunk(3) as $date)
        <div class="row">
          @foreach ($date as $yearmonth)
          <div class="col-xs-4">
            <span class="text-bold">
              <a href="{{ route('billing.pastbilling', ['year' => $yearmonth['year'], 'month' => $yearmonth['month']]) }}">
              {{ date('Y-M', mktime(0,0,0, $yearmonth['month'], 1, $yearmonth['year'])) }}</a>
            </span>
          </div>
          @endforeach
        </div>
      @endforeach
    </div>
  </div>

  <div class="nav-tabs-custom">
    <ul class="nav nav-tabs pull-right bg-gray">
      <li><a href="#tab3" data-toggle="tab">{{ trans('billing::billing.summary-ind-mining') }}</a></li>
      <li><a href="#tab2" data-toggle="tab">{{ trans('billing::billing.summary-corp-pve') }}</a></li>
      <li class="active"><a href="#tab1" data-toggle="tab">{{ trans('billing::billing.summary-corp-mining') }}</a></li>
      <li class="pull-left header">
        <i class="fa fa-history"></i> Previous Bills
      </li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane active" id="tab1">
        <table class="table table-striped">
          <tr>
            <th>Corporation</th>
            <th>Mined Amount</th>
            <th>Percentage of Market Value</th>
            <th>Adjusted Value</th>
            <th>Tax Rate</th>
            <th>Tax Owed</th>
            <th>Paid</th>
          </tr>
          @foreach($stats as $row)
            <tr>
              <td>{{ $row->corporation->name }}</td>
              <td>{{ number_format($row->mining_bill, 2) }}</td>
              <td>{{ $row->mining_modifier }}%</td>
              <td>{{ number_format(($row->mining_bill * ($row->mining_modifier / 100)),2) }}</td>
              <td>{{ $row->mining_taxrate }}%</td>
              <td>{{ number_format((($row->mining_bill * ($row->mining_modifier / 100)) * ($row->mining_taxrate / 100)),2) }}</td>
              <td>
                @if ($row->isMiningPaid() || $row->isPaid())
                <i class="fa fa-check-circle text-green"></i>
                @else
                <i class="fa fa-close text-red"></i>
                @endif
              </td>
            </tr>
          @endforeach
        </table>
      </div>
      <div class="tab-pane" id="tab2">
        <table class="table table-striped">
          <tr>
            <th>Corporation</th>
            <th>Total Bounties</th>
            <th>Tax Rate</th>
            <th>Tax Owed</th>
            <th>Paid</th>
          </tr>
          @foreach($stats as $row)
            <tr>
              <td>{{ $row->corporation->name }}</td>
              <td>{{ number_format($row->pve_bill, 2) }}</td>
              <td>{{ $row->pve_taxrate }}%</td>
              <td>{{ number_format(($row->pve_bill * ($row->pve_taxrate / 100)),2) }}</td>
              @if ($row->isPvePaid() || $row->isPaid())
                <td><i class="fa fa-check-circle text-green"></i></td>
              @else
                <td><i class="fa fa-close text-red"></i></td>
              @endif
            </tr>
          @endforeach
        </table>
      </div>
      <div class="tab-pane" id="tab3">
        <div class="col-md-6">
          <select class="select" style="width: 50%" id="corpspinner">
            <option disabled selected value="0">Please Choose a Corp</option>
            @foreach($stats as $row)
              <option value="{{ $row->corporation->corporation_id }}">{{ $row->corporation->name }}</option>
            @endforeach
          </select>
        </div>
        <table class="table compact table-condensed table-hover table-responsive table-striped" id='indivmining'>
          <thead>
          <tr>
            <th>Character Name</th>
            <th>Mining Amount</th>
            <th>Market Modifier</th>
            <th>Mining Tax</th>
            <th>Tax Due</th>
          </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>

@endsection

@push('javascript')
  @include('web::includes.javascript.id-to-name')

  <script type="application/javascript">

      table = $('#indivmining').DataTable({
          paging: false,
      });

      ids_to_names();

      $('#corpspinner').change(function () {

          $('#indivmining').find('tbody').empty();
          id = $('#corpspinner').find(":selected").val();
          year = $('#year').val();
          month = $('#month').val();

          if (id > 0) {
              $.ajax({
                  headers: function () {
                  },
                  url: "/billing/getindpastbilling/" + id + "/" + year + "/" + month,
                  type: "GET",
                  dataType: 'json',
                  timeout: 10000
              }).done(function (result) {
                  if (result) {
                      table.clear();
                      for (var chars in result) {
                          table.row.add(['<a href=""><span class="id-to-name" data-id="' + result[chars].character_id + '">{{ trans('web::seat.unknown') }}</span></a>', (new Intl.NumberFormat('en-US').format(result[chars].mining_bill)),
                              (result[chars].mining_modifier) + "%", (result[chars].mining_taxrate) + "%",
                              (new Intl.NumberFormat('en-US', {maximumFractionDigits: 2}).format(result[chars].mining_bill * (result[chars].mining_modifier / 100) * (result[chars].mining_taxrate / 100))) + " ISK"]);
                      }
                      table.draw();
                      ids_to_names();
                  }
              });
          }
      });

      $(document).ready(function () {
          $('#corpspinner').select2();
      });
  </script>
@endpush
