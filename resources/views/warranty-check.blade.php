@extends('layouts.user')
@section('title', 'Warranty Check')

@section('style')
<style>
    /* Shared with trade-in info page */
    .ti-terms-wrapper {
        padding: 60px 0 20px;
        background: #fff;
    }
    .ti-terms-card {
        background: #f3f3f3;
        border-radius: 20px;
        padding: 48px 56px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
    }
    .ti-terms-card h2 {
        font-weight: 700;
        margin-bottom: 28px;
        color: #333;
        font-size: 2.5rem;
        line-height: 1.2;
    }
    @media (max-width: 768px) {
        .ti-terms-card {
            padding: 32px 24px;
        }
        .ti-terms-card h2 {
            font-size: 2rem;
        }
    }
    .ti-info-hero {
        background: #f8f8f8;
        padding: 60px 0;
        text-align: center;
        border-bottom: 1px solid #eee;
    }
    .ti-info-hero h1 {
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
        font-size: clamp(2.35rem, 5.5vw, 3.5rem);
        line-height: 1.18;
    }
    .ti-info-hero p {
        font-size: 1.75rem;
        line-height: 1.55;
        color: #444;
        max-width: 820px;
        margin: 0 auto;
    }
    .ti-policy-section {
        background: #fff;
        padding: 40px 0 60px;
    }
    .ti-policy-box {
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 38px 42px;
        margin-bottom: 28px;
        background: #fff;
    }
    .ti-policy-box h3 {
        font-weight: 700;
        color: #D10024;
        margin-bottom: 24px;
        border-bottom: 2px solid #D10024;
        display: inline-block;
        padding-bottom: 8px;
        font-size: 1.9rem;
    }
    .ti-cta-btn {
        display: inline-block;
        padding: 18px 52px;
        background-color: #D10024;
        color: #FFF !important;
        font-weight: 700;
        border: none;
        border-radius: 40px;
        text-transform: uppercase;
        transition: all 0.2s;
        font-size: 1.35rem;
        letter-spacing: 0.04em;
        text-decoration: none;
        cursor: pointer;
    }
    .ti-cta-btn:hover {
        background-color: #E60026;
        color: #FFF !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(209, 0, 36, 0.3);
    }
    /* Warranty-specific */
    .ti-terms-card .form-control {
        font-size: 1.3rem;
        padding: 16px 20px;
        min-height: 60px;
    }
    .ti-terms-card .invalid-feedback {
        font-size: 1.2rem;
    }
    .ti-terms-card small.text-muted,
    .ti-terms-card .warranty-hint {
        font-size: 1.25rem !important;
        line-height: 1.55;
    }
    .ti-terms-wrapper .alert {
        font-size: 1.35rem;
        line-height: 1.55;
        padding: 22px 26px;
    }
    .ti-terms-wrapper .ti-terms-list {
        padding-left: 1.65rem;
        margin-bottom: 0;
    }
    .ti-terms-wrapper .ti-terms-list li {
        margin-bottom: 22px;
        font-size: 1.35rem;
        line-height: 1.65;
        color: #1a1a1a;
    }
    .ti-policy-box p.text-muted {
        font-size: 1.35rem !important;
        line-height: 1.6;
    }
    .ti-policy-box p.small,
    .ti-policy-box .small {
        font-size: 1.2rem !important;
        line-height: 1.55;
    }
    .mono-imei {
        font-family: ui-monospace, monospace;
        letter-spacing: 0.03em;
        font-size: 1.3rem;
    }
    .result-table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .result-table td:first-child {
        width: 38%;
        max-width: 260px;
        color: #333;
        font-size: 1.15rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 16px 24px 16px 0;
        margin: 0;
    }
    .result-table td:last-child {
        padding: 16px 0 16px 16px;
        vertical-align: top;
        font-size: 1.35rem;
        color: #0d0d0d;
    }
    .result-table td {
        border-bottom: 1px solid #eee;
        vertical-align: top;
    }
    .result-table tr:last-child td {
        border-bottom: none;
    }
    .badge-warranty-active { background: #198754; }
    .badge-warranty-expired { background: #6c757d; }
    .terms-img-wrap {
        border: 1px solid #eee;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }
    .terms-img-wrap img {
        width: 100%;
        height: auto;
        display: block;
    }
    .warranty-form-label {
        font-weight: 600;
        color: #222;
        margin-bottom: 14px;
        font-size: 1.5rem;
    }
    .ti-terms-wrapper .badge {
        font-size: 1.15rem;
        padding: 0.55em 1em;
    }
    .warranty-stack-top {
        margin-bottom: 24px;
    }
    .warranty-stack-bottom {
        margin-top: 8px;
    }
    .warranty-photo-box h3 {
        font-size: 1.9rem;
        font-weight: 700;
        color: #D10024;
    }
    .warranty-photo-box {
        text-align: center;
        padding: 20px;
    }
    .warranty-photo-box img {
        max-width: 100%;
        max-height: 420px;
        width: auto;
        height: auto;
        object-fit: contain;
        border-radius: 8px;
    }
    .warranty-photo-placeholder {
        min-height: 320px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f0f0f0;
        border-radius: 8px;
        color: #999;
        font-size: 68px;
    }
    @media (max-width: 767px) {
        .warranty-result-photo-col {
            margin-bottom: 20px;
        }
    }
    @media (max-width: 576px) {
        .ti-info-hero p {
            font-size: 1.4rem;
        }
    }
</style>
@endsection

@section('content')
{{-- Hero (same pattern as tradeInInfo) --}}
<div class="ti-info-hero">
    <div class="container">
        <h1>Warranty check</h1>
        <p>Enter your device IMEI below to view warranty status and device information. Terms and conditions are shown until you look up a device.</p>
    </div>
</div>

{{-- Main: IMEI top → results → terms below; full width --}}
<div class="ti-terms-wrapper">
    <div class="container">
        {{-- TOP: IMEI lookup --}}
        <div class="row">
            <div class="col-12">
                <div class="ti-terms-card warranty-stack-top">
                    <h2>Look up by IMEI</h2>
                    <form method="post" action="{{ route('warranty.check') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="imei" class="warranty-form-label">IMEI</label>
                            <input type="text" name="imei" id="imei"
                                class="form-control mono-imei @error('imei') is-invalid @enderror"
                                value="{{ $imeiInput }}" placeholder="e.g. 352000001234561" autocomplete="off" maxlength="32">
                            @error('imei')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-2">Find IMEI on the box, under the battery, or dial *#06# on the phone.</small>
                        </div>
                        <br>
                        <button type="submit" class="ti-cta-btn">
                            <i class="fa fa-search me-1"></i> Check warranty
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Results (after search) --}}
        @if($notFound)
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning" style="border-radius: 8px;">
                    <i class="fa fa-exclamation-triangle me-1"></i>
                    No device was found for this IMEI. Please check the number and try again, or contact Flexicell support.
                </div>
            </div>
        </div>
        @endif

        @if($device)
        <div class="row">
            <div class="col-md-5 col-lg-4 warranty-result-photo-col">
                <div class="ti-policy-section" style="padding:0;">
                    <div class="ti-policy-box warranty-photo-box mb-0">
                        <h3 class="w-100 d-block text-start mb-3" style="border-bottom: 2px solid #D10024; padding-bottom: 8px;">Device</h3>
                        @if($device->product)
                            <img src="{{ $device->product->imageUrl() }}" alt="{{ $device->product->phoneModel?->model_name ?? 'Device' }}">
                        @else
                            <div class="warranty-photo-placeholder">
                                <i class="fa fa-mobile"></i>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-7 col-lg-8">
                <div class="ti-policy-section" style="padding-top:0;">
                    <div class="ti-policy-box">
                        <h3>Device details</h3>
                        <table class="result-table w-100">
                            <tr>
                                <td>IMEI</td>
                                <td class="mono-imei fw-semibold">{{ $device->imei }}</td>
                            </tr>
                            <tr>
                                <td>Model</td>
                                <td>{{ $device->product?->phoneModel?->model_name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td>Brand</td>
                                <td>{{ $device->product?->phoneModel?->brand?->brand_name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td>Category</td>
                                <td>{{ $device->product?->phoneModel?->category?->category_name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td>RAM</td>
                                <td>{{ $device->ramOption?->name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td>Storage</td>
                                <td>{{ $device->storageOption?->name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td>Color</td>
                                <td>{{ $device->colorOption?->name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td>Condition</td>
                                <td>{{ $device->product?->product_type ? ucfirst($device->product->product_type) : '—' }}</td>
                            </tr>
                            @if($device->selling_price)
                            <tr>
                                <td>Price</td>
                                <td>{{ number_format($device->selling_price) }} Ks</td>
                            </tr>
                            @endif
                        </table>
                    </div>

                    <div class="ti-policy-box">
                        <h3>Warranty</h3>
                        @if($warrantyDetail)
                            @php
                                $active = !$warrantyDetail->is_expired;
                            @endphp
                            <table class="result-table w-100">
                                <tr>
                                    <td>Status</td>
                                    <td>
                                        <span class="badge {{ $active ? 'badge-warranty-active' : 'badge-warranty-expired' }} text-white px-3 py-2">
                                            {{ $active ? 'Active' : 'Expired' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Plan</td>
                                    <td>{{ $warrantyDetail->warranty?->warranty_month ?? '—' }} months</td>
                                </tr>
                                <tr>
                                    <td>Start date</td>
                                    <td>{{ $warrantyDetail->start_date?->format('d M Y') ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td>End date</td>
                                    <td>{{ $warrantyDetail->end_date?->format('d M Y') ?? '—' }}</td>
                                </tr>
                                @if($warrantyDetail->customer)
                                <tr>
                                    <td>Registered to</td>
                                    <td>{{ $warrantyDetail->customer->name }}</td>
                                </tr>
                                @endif
                            </table>
                        @else
                            <p class="text-muted mb-0">
                                No warranty registration was found for this device yet. If you recently purchased it, please allow time for activation or contact the shop.
                            </p>
                            @if($device->warranty)
                                <p class="mt-2 mb-0 small text-muted">
                                    Device warranty option on file: <strong>{{ $device->warranty->warranty_month }} months</strong> (shop policy).
                                </p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Terms & conditions: only when no successful device result --}}
        @if(!$device)
        <div class="row">
            <div class="col-12">
                <div class="ti-terms-card warranty-stack-bottom">
                    <h2>Terms &amp; conditions</h2>

                    <ul class="ti-terms-list">
                        <li>ဖုန်းပစ္စည်းများအား Phone နှင့် Tablet များ၊ နှစ်အာမခံ (၁)ရက်ကျော်ပြီးသည်များသည်လည်းကောင်း၊ သက်တမ်းမပြည့်မီ (နှစ်) software Update ကြောင့် ဖြစ်ပေါ်လာသော Software/Hardware Error များအား တာဝန်မယူဘဲ မပြန်ပါ။</li>

                        <li>နှစ်အာမခံ (၁)ရက်ကျော်ပြီးသည်နှင့် Error နှင့် Factory Error အားလက်လအတွင်း User ကြောင့်ဖြစ်ပေါ်လာသော Error များအား လုံးဝ တာဝန်မယူတော့ကြောင်း အသိပေးပါသည်။</li>

                        <li>တစ်နှစ်အတွင်းရောင်းချပြီး ပစ္စည်းများကိုသာ အာမခံပေးပါမည်။ မတော်တဆ ကွဲပဲ့ ပျက်စီးခြင်း၊ မူရင်းကော်ပတ် ပျက်စီးခြင်းများ အာမခံ ကာလမပေးပါ။</li>

                        <li>ဖုန်းပစ္စည်းများအား Second မျိုးကွဲအားဖြင့် ပြင်ဆင်ပြီးရောင်းချရသော (၅၀%) ရှိရာ အရောင်းပြီးနောက် ပြန်တင်ပစ္စည်း၊ (၃၀%) ရှိရာ ရောင်းပြီးနောက် ပြန်လဲပစ္စည်းဖြစ်ကြောင်း မေးမြန်းပေးပါသည်။</li>

                        <li>ဖုန်းပစ္စည်းများအား Second မျိုးကွဲအားဖြင့် အာမခံပြီးရောင်းချရသော (၅၀%) ရှိရာ အရောင်းပြီးနောက် ပြန်လဲပစ္စည်းဖြစ်ပြီး (၃၀%) ရှိရာ အရောင်းပြီးနောက် ပြန်လဲပစ္စည်းဖြစ်ကြောင်း အသိပေးပါသည်။</li>

                        <li>ဖုန်းပစ္စည်းများအား Brand New/Packing မျိုးကွဲအားဖြင့် ပြန်လည်ရောင်းချရသော Second ကာလအတွင်းရောင်းချရသော (၁၀%) ရှိရာ အရောင်းပြီးနောက် ပြန်လဲပစ္စည်းဖြစ်ကြောင်း အသိပေးပါသည်။</li>

                        <li>iPhone မျိုးစုံ Model များအား Factory Error အရသာ (၁)လ ဆိုင်သို့ ပေးပို့ပါသည်။ အားလုံး (၁)လ တာဝန်ခံအာမခံဖြစ်သောကြောင့် Factory Error မဟုတ်သော Service ကောက်ခံ သတ်မှတ်ခြင်းရှိပါသည်။</li>

                        <li>ဖုန်းပစ္စည်းများအား အသုံးပြုပြီးပစ္စည်းများတွင် ပုံးနှင့် Seal မရှိဘဲ မူလအတိုင်း မပြသပါက မူလအတိုင်း Error ကို တာဝန်မယူတော့ကြောင်း အသိပေးပါသည်။</li>
                    </ul>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
