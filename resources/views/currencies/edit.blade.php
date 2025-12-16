@extends('layouts.app') @section('content')
    <div class="container-fluid px-1">
        <div class="row">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="mb-0">Edit Currency</h2>
                </div>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br /><br />
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="alert alert-danger">
            <p>Please use a valid currency name to avoid payment errors. The supported currencies are: <br> usd, aed, afn, all, amd, ang, aoa, ars, aud, awg, azn, bam, bbd, bdt, bgn, bhd, bif, bmd, bnd, bob, brl, bsd, bwp, byn, bzd, cad, cdf, chf, clp, cny, cop, crc, cve, czk, djf, dkk, dop, dzd, egp, etb, eur, fjd, fkp, gbp, gel, gip, gmd, gnf, gtq, gyd, hkd, hnl, hrk, htg, huf, idr, ils, inr, isk, jmd, jod, jpy, kes, kgs, khr, kmf, krw, kwd, kyd, kzt, lak, lbp, lkr, lrd, lsl, mad, mdl, mga, mkd, mmk, mnt, mop, mur, mvr, mwk, mxn, myr, mzn, nad, ngn, nio, nok, npr, nzd, omr, pab, pen, pgk, php, pkr, pln, pyg, qar, ron, rsd, rub, rwf, sar, sbd, scr, sek, sgd, shp, sle, sos, srd, std, szl, thb, tjs, tnd, top, try, ttd, twd, tzs, uah, ugx, uyu, uzs, vnd, vuv, wst, xaf, xcd, xof, xpf, yer, zar, zmw, usdc, btn, ghs, eek, lvl, svc, vef, ltl, sll, mro</p>
        </div>
        <form action="{{ route('currencies.update', $currency->id) }}" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="url" value="{{ url()->previous() }}">

            <div class="card mt-3">
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-12 col-md-4 mb-3">
                            <label class="font-weight-bold"><span class="text-danger">*</span> Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old( 'name', $currency->name) }}" placeholder="Name">
                        </div>

                        <div class="form-group col-12 col-md-4 mb-3">
                            <label class="font-weight-bold"><span class="text-danger">*</span> Symbol</label>
                            <input type="text" name="symbol" class="form-control" value="{{ old('symbol' , $currency->symbol) }}" placeholder="Symbol">
                        </div>

                        <div class="form-group col-12 col-md-4 mb-3">
                            <label class="font-weight-bold"><span class="text-danger">*</span> Rate</label>
                            <input type="text" name="rate" class="form-control" value="{{ old( 'rate' ,$currency->rate) }}" placeholder="Rate">
                        </div>

                        <div class="col-12 text-center mt-2">
                            <button type="submit" class="btn btn-md btn-primary shadow-sm float-end font-weight-bold">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
