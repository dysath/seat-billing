@extends('web::layouts.grids.6-6')

@section('title', trans('billing::billing.settings'))
@section('page_header', trans('billing::billing.settings'))

@section('left')
<div class="box box-success box-solid">
    <form class="form" method="POST" action="{{ route('billing.savesettings')  }}">
    {{ csrf_field() }}
    <div class="box-header">
        <h3 class="box-title">{{ trans('billing::billing.settings') }}</h3>
    </div>
    <div class="box-body">
        <table class="table table-condensed">
        <tr>
            <td><label class="">Ore value modifier</label></td>
            <td><input class="input" type="text" name="oremodifier" size="3" value="{{ setting('oremodifier', true) }}">%</td>
        </tr>
        <tr>
            <td><label class="">Standard Ore TAX Rate</label></td>
            <td><input class="input" type="text" name="oretaxrate" size="3" value="{{ setting('oretaxrate', true) }}">%</td>
        </tr>
        <tr>
            <td><label class="">Standard Bounty TAX Rate</label></td>
            <td><input class="input" type="text" name="bountytaxrate" size="3" value="{{ setting('bountytaxrate', true) }}">%</td>
        </tr>
        <tr>
            <td><label class="">Incentivized Ore value modifier</label></td>
            <td><input class="input" type="text" name="ioremodifier" size="3" value="{{ setting('ioremodifier', true) }}">%</td>
        </tr>
        <tr>
            <td><label class="">Incentivized Standard Ore TAX Rate</label></td>
            <td><input class="input" type="text" name="ioretaxrate" size="3" value="{{ setting('ioretaxrate', true) }}">%</td>
        </tr>
        <tr>
            <td><label class="">Incentivized Standard Bounty TAX Rate </label></td>
            <td><input class="input" type="text" name="ibountytaxrate" size="3" value="{{ setting('ibountytaxrate', true) }}">%</td>
        </tr>
        <tr>
            <td><label class="">Percentage of registered characters to meet Incentivized Rates</label></td>
            <td><input class="input" type="text" name="irate" size="3" value="{{ setting('irate', true) }}">%</td>
        </tr>
        </table>
    </div>
    <div class="box-footer">
        <input class="btn pull-right" type="submit" value="Update">
    </div>
    </form>
</div>

@endsection

