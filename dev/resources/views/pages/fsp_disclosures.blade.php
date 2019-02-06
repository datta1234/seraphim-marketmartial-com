@extends('layouts.app')

@section('content')
{{-- About content Card --}}
@component('partials.content_card')
    @slot('header')
        <h2><span class="icon icon-globe"></span></h2>
    @endslot
    @slot('body')
        <div class="row">
            <div class="col">
                
                <h5 class="mb-5">Market Martial is owned and operated by Seraphim Financial Services (Pty) Ltd (“Seraphim”), an authorized financial services provider.</h5>
                
                <table>
                    <tr>
                        <td><strong>FSP Number:</strong></td>
                        <td>49407</td>
                    </tr>
                    <tr>
                        <td><strong>Registration Number:&nbsp;&nbsp;&nbsp;</strong></td>
                        <td>2017/511139/07</td>
                    </tr>
                    <tr>
                        <td><strong>LEI Number:</strong></td>
                        <td>894500BST0FYEQBT7307</td>
                    </tr>
                    <tr>
                        <td><strong>Contact Person:</strong></td>
                        <td>Wade Bothwell</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td><a href="mailto:TheMartial@MarketMartial.com">TheMartial@MarketMartial.com</a></td>
                    </tr>
                    <tr>
                        <td><strong>Telephone:</strong></td>
                        <td><a href="tel:082-784-6004">082 784 6004</a></td>
                    </tr>
                </table>

                <h5 class="mt-5">Compliance Officer</h5>
                <table>
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>Masthead (Pty) Ltd</td>
                    </tr>
                    <tr>
                        <td><strong>Telephone:&nbsp;&nbsp;&nbsp;</strong></td>
                        <td><a href="tel:021-686-3588">021 686 3588</a></td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td><a href="mailto:info@masthead.co.za">info@masthead.co.za</a></td>
                    </tr>
                    <tr>
                        <td><strong>Website:</strong></td>
                        <td><a href="www.masthead.co.za">www.masthead.co.za</a></td>
                    </tr>
                </table>

                <h5 class="mt-5">Conflict of Interest Policy</h5>
                <p>Seraphim has adopted and implemented a Conflict of Interest Policy that complies with the provisions of the FAIS Act.</p>
                <a class="" target="_blank" href="{{ action('PDFController@conflictsOfInterestPolicy') }}">Click Here</a>


                <h5 class="mt-5">Complaints Procedure</h5>
                <p>Should you wish to pursue a complaint against a key individual or representative of Seraphim, you should address the complaint in writing to us at <a href="mailto:TheMartial@MarketMartial.com">TheMartial@MarketMartial.com</a>.</p>
                <p>If you cannot settle your complaint with us, you are entitled to refer it to the Office of the FAIS Ombud, at <a href="mailto:info@faisombud.co.za">info@faisombud.co.za</a> or telephone number <a href="tel:0860-324-766">0860 324 766</a>.</p>


                <h5 class="mt-5">TCF Policy</h5>
                <p>Seraphim’s Treating Customers Fairly policy is centred around the guidelines provided by the Financial Sector Conduct Authority (FSCA) to ensure we consistently deliver fair outcomes to our clients and take responsibility for the business and staff providing an enhanced service quality to clients, based on a culture of openness and transparency. As a business, we take the requirements of the FSCA seriously, in particular, the requirement to treat customers fairly.</p>

                <h5 class="mt-5">Disclaimer</h5>
                <p>Seraphim has and will continue to take reasonable care to ensure that all information, in so far as this is under its control, provided on this website is true and correct.</p>
                <p>Seraphim shall not be responsible for, and therefore disclaims any liability for, any loss, liability, damage (whether direct or consequential) or expense of any nature whatsoever which may be suffered as a result of or which may be attributable, directly or indirectly, to the use of or reliance upon any information, links or service provided through this website.</p>
                <p>There is no warranty of any kind, expressed or implied, regarding the information or any aspect of this service. Any warranty implied by law is hereby excluded except to the extent such exclusion would be unlawful.</p>

            </div>
        </div>
    @endslot
@endcomponent
<div class="push-down"></div>
@endsection