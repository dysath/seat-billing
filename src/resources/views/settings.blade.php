@extends('web::layouts.grids.6-6')

@section('title', trans('billing::billing.settings'))
@section('page_header', trans('billing::billing.settings'))

@section('left')
<div class="box box-success box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">{{ trans('billing::billing.settings') }}</h3>
    </div>
    <form method="POST" action="{{ route('billing.savesettings')  }}" class="form-horizontal">
        <div class="box-body">
            {{ csrf_field() }}
            <h4>Default Settings</h4>
            <div class="form-group">
                <label for="oremodifier" class="col-sm-3 control-label">Ore value modifier</label>
                <div class="col-sm-7">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="oremodifier" id="oremodifier" size="3" value="{{ setting('oremodifier', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="oretaxrate" class="col-sm-3 control-label">Ore TAX Rate</label>
                <div class="col-sm-7">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="oretaxrate" id="oretaxrate" size="3" value="{{ setting('oretaxrate', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="bountytaxrate" class="col-sm-3 control-label">Bounty TAX Rate</label>
                <div class="col-sm-7">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="bountytaxrate" id="bountytaxrate" size="3" value="{{ setting('bountytaxrate', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <hr />
            <h4>Incentivized Settings</h4>
            <div class="form-group">
                <label for="ioremodifier" class="col-sm-3 control-label">Ore value modifier</label>
                <div class="col-sm-7">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="ioremodifier" id="ioremodifier" size="3" value="{{ setting('ioremodifier', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="ioretaxrate" class="col-sm-3 control-label">Ore TAX Rate</label>
                <div class="col-sm-7">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="ioretaxrate" id="ioretaxrate" size="3" value="{{ setting('ioretaxrate', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="ibountytaxrate" class="col-sm-3 control-label">Bounty TAX Rate </label>
                <div class="col-sm-7">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="ibountytaxrate" id="ibountytaxrate" size="3" value="{{ setting('ibountytaxrate', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="irate" class="col-sm-3 control-label">Rates Threshold</label>
                <div class="col-sm-7">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="irate" id="irate" size="3" value="{{ setting('irate', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                    <p class="help-block">Percentage of registered characters to meet Incentivized Rates</p>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <input class="btn btn-success pull-right" type="submit" value="Update">
        </div>
    </form>
</div>

@endsection

