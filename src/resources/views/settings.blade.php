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
                <div class="col-sm-8">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="oremodifier" id="oremodifier" size="4" value="{{ setting('oremodifier', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="oretaxrate" class="col-sm-3 control-label">Ore TAX Rate</label>
                <div class="col-sm-8">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="oretaxrate" id="oretaxrate" size="4" value="{{ setting('oretaxrate', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="ioretaxrate" class="col-sm-3 control-label">Ore Refining Rate</label>
                <div class="col-sm-8">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="refinerate" id="refinerate" size="4" value="{{ setting('refinerate', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="bountytaxrate" class="col-sm-3 control-label">Bounty TAX Rate</label>
                <div class="col-sm-8">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="bountytaxrate" id="bountytaxrate" size="4" value="{{ setting('bountytaxrate', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <hr />
            <h4>Incentivized Settings</h4>
            <div class="form-group">
                <label for="ioremodifier" class="col-sm-3 control-label">Ore value modifier</label>
                <div class="col-sm-8">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="ioremodifier" id="ioremodifier" size="4" value="{{ setting('ioremodifier', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="ioretaxrate" class="col-sm-3 control-label">Ore TAX Rate</label>
                <div class="col-sm-8">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="ioretaxrate" id="ioretaxrate" size="4" value="{{ setting('ioretaxrate', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="ibountytaxrate" class="col-sm-3 control-label">Bounty TAX Rate </label>
                <div class="col-sm-8">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="ibountytaxrate" id="ibountytaxrate" size="4" value="{{ setting('ibountytaxrate', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="irate" class="col-sm-3 control-label">Rates Threshold</label>
                <div class="col-sm-8">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="irate" id="irate" size="4" value="{{ setting('irate', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                    <p class="help-block">Percentage of registered characters to meet Incentivized Rates</p>
                </div>
            </div>
            <hr />
            <h4>Valuation of Ore</h4>
            <div class="form-group">
                <label for="ibountytaxrate" class="col-sm-3 control-label">Value at Ore Price</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        @if (setting('pricevalue', true) == "o")
                        <input type="radio" name="pricevalue" id="pricevalue" value="o" checked/>
                        @else
                        <input type="radio" name="pricevalue" id="pricevalue" value="o"/>
                        @endif
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="ibountytaxrate" class="col-sm-3 control-label">Value at Mineral Price</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        @if (setting('pricevalue', true) == "m")
                        <input class="radio" type="radio" name="pricevalue" id="pricevalue" value="m" checked/>
                        @else
                        <input type="radio" name="pricevalue" id="pricevalue" value="m"/>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <input class="btn btn-success pull-right" type="submit" value="Update">
        </div>
    </form>
</div>
@endsection

@section('right')
<div class="box box-success box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">{{ trans('billing::billing.settings') }}</h3>
    </div>
    <div class="box-body">
        <div class="col-sm-12">
            <p><label>Ore value modifier:</label> This is a modifier used on the base costs of the ore/minerals/goo to adjust for inflation/deflation during the billing period.  Normally this is 90-95% </p>
        </div>
        <div class="col-sm-12">
            <p><label>Ore Tax Rate:</label> Rate to tax on value of the mined materials. </p>
        </div>
        <div class="col-sm-12">
            <p><label>Ore Refining Rate:</label> This should be the max refine amount in your area.  Max rates with RX-804 implant, level V skills, and a T2 Rigged Tatara is 89.4%.  Adjust this as you see fit, but I recommend using the maximum rate available to your members in your area of space.</p>
        </div>
        <div class="col-sm-12">
            <p><label>Bounty Tax Rate:</label> Rate of ratting bounties to tax.  Usually 5-10%</p>
        </div>
        <div class="col-sm-12">
            <p><label>Incentivised Rates:</label> Incentivised rates are on a per-corporation basis only.  These are discounted rates based on the number of members in the corp have signed up on Seat, including all alts.  If they're not signed up on SeAT, the alliance is not seeing their mining amounts, therefore, they get higher tax rates.</p>
        </div>
        <div class="col-sm-12">
            <p><label>Valuation of Ore:</label> Value of ore can be determined with two methods:  By ore type OR By mineral content.  If you are moon mining, it's better to use mineral content as it's more accurate as Moon Goo is rarely sold by the raw ore, but more often as refined products.  This keeps the moon mining honest.</p>
        </div>
    </div>
</div>
@endsection
