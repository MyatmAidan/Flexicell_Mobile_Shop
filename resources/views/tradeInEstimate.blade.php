@extends('layouts.user')
@section('title', 'Trade-In Estimate')

@section('style')
<style>
    /* ---- Stepper ---- */
    .ti-step-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 30px 0 10px;
        gap: 0;
    }
    .ti-step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        max-width: 140px;
    }
    .ti-step-num {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #e5e5e5;
        color: #888;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.3rem;
        border: 2px solid #ddd;
        transition: all .3s;
    }
    .ti-step-item.active .ti-step-num { background: #D10024; color: #fff; border-color: #D10024; }
    .ti-step-item.done   .ti-step-num { background: #D10024; color: #fff; border-color: #D10024; }
    .ti-step-lbl {
        font-size: 1.5rem;
        color: #888;
        margin-top: 5px;
        text-align: center;
        font-weight: 600;
    }
    .ti-step-item.active .ti-step-lbl,
    .ti-step-item.done   .ti-step-lbl { color: #D10024; }
    .ti-connector {
        flex: 1;
        height: 2px;
        background: #ddd;
        margin-bottom: 26px;
        transition: background .3s;
    }
    .ti-connector.done { background: #D10024; }

    /* ---- Device type cards (step 0) ---- */
    .ti-device-grid {
        display: flex;
        gap: 16px;
        justify-content: center;
        flex-wrap: wrap;
        margin: 24px 0;
    }
    .ti-device-card {
        border: 2px solid #e5e5e5;
        border-radius: 6px;
        background: #fff;
        width: 160px;
        padding: 28px 16px 20px;
        text-align: center;
        cursor: pointer;
        transition: border-color .2s, background .2s, box-shadow .2s;
    }
    .ti-device-card:hover {
        border-color: #D10024;
        background: #fff5f5;
        box-shadow: 0 2px 10px rgba(209,0,36,.1);
    }
    .ti-device-card.selected {
        border-color: #D10024;
        background: #fff0f0;
    }
    .ti-device-card i {
        font-size: 2.9rem;
        color: #bbb;
        margin-bottom: 10px;
        display: block;
        transition: color .2s;
    }
    .ti-device-card:hover i,
    .ti-device-card.selected i { color: #D10024; }
    .ti-device-card .ti-device-name {
        font-size: 1.3rem;
        font-weight: 700;
        color: #333;
    }
    .ti-device-card.selected .ti-device-name { color: #D10024; }

    /* ---- Option cards ---- */
    .ti-option {
        border: 1px solid #e5e5e5;
        border-radius: 4px;
        padding: 14px 18px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: border-color .2s, background .2s;
    }
    .ti-option:hover    { border-color: #D10024; background: #fff5f5; }
    .ti-option.selected { border-color: #D10024; background: #fff0f0; }
    .ti-option-title  { font-weight: 700; font-size: 1.6rem; color: #333; margin-bottom: 3px; }
    .ti-option.selected .ti-option-title { color: #D10024; }
    .ti-option-desc   { font-size: 1.3rem; color: #888; }

    /* ---- Question labels ---- */
    .ti-q-label {
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 10px;
        margin-top: 20px;
        color: #333;
    }

    /* ---- Model grid ---- */
    .ti-model-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        max-height: 360px;
        overflow-y: auto;
        margin-top: 12px;
    }
    .ti-model-btn {
        padding: 13px 8px;
        border: 1px solid #e5e5e5;
        border-radius: 4px;
        background: #fff;
        font-size: 1.3rem;
        font-weight: 600;
        color: #333;`
        cursor: pointer;
        text-align: center;
        transition: border-color .2s, background .2s, color .2s;
        font-family: 'Montserrat', sans-serif;
    }
    .ti-model-btn:hover    { border-color: #D10024; color: #D10024; }
    .ti-model-btn.selected { border-color: #D10024; background: #D10024; color: #fff; }

    /* ---- Battery badge ---- */
    .ti-battery-badge {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 3px;
        font-size: 1.3rem;
        font-weight: 700;
        background: #d4edda;
        color: #155724;
    }
    .ti-battery-badge.fair { background: #fff3cd; color: #856404; }
    .ti-battery-badge.poor { background: #f8d7da; color: #721c24; }

    /* Battery input override */
    #batteryInput { font-size: 1.2rem !important; }

    /* Search input */
    #modelSearch { font-size: 1.5rem !important; }

    /* ---- Grade badges ---- */
    .grade-S { background: #d4edda; color: #155724; }
    .grade-A { background: #cce5ff; color: #004085; }
    .grade-B { background: #fff3cd; color: #856404; }
    .grade-C { background: #f8d7da; color: #721c24; }
    .ti-grade-badge {
        display: inline-block;
        padding: 5px 20px;
        border-radius: 3px;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 16px;
    }

    /* ---- Price result ---- */
    .ti-price-val {
        font-size: 2rem;
        font-weight: 800;
        color: #D10024;
        margin: 4px 0;
    }
    .ti-notice {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 4px;
        padding: 12px 16px;
        font-size: 1rem;
        color: #856404;
        margin-top: 16px;
    }

    @media (max-width: 768px) {
        .ti-model-grid  { grid-template-columns: repeat(2, 1fr); }
        .ti-device-card { width: 130px; }
    }
</style>
@endsection

@section('content')

{{-- Page Heading --}}
<div class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-title">
                    <h3 class="title">Device Trade-In Estimate</h3>
                </div>
                <p style="color:#888;margin-top:-10px;margin-bottom:0;">
                    Get your device's estimated trade-in value in easy steps.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Stepper (4 steps now) --}}
<div class="container">
    <div class="ti-step-bar" id="tiStepper">
        <div class="ti-step-item active" id="sdot1">
            <div class="ti-step-num" id="snum1">1</div>
            <div class="ti-step-lbl">Device Type</div>
        </div>
        <div class="ti-connector" id="conn1"></div>
        <div class="ti-step-item" id="sdot2">
            <div class="ti-step-num" id="snum2">2</div>
            <div class="ti-step-lbl">Select Model</div>
        </div>
        <div class="ti-connector" id="conn2"></div>
        <div class="ti-step-item" id="sdot3">
            <div class="ti-step-num" id="snum3">3</div>
            <div class="ti-step-lbl">Assess Condition</div>
        </div>
        <div class="ti-connector" id="conn3"></div>
        <div class="ti-step-item" id="sdot4">
            <div class="ti-step-num" id="snum4">4</div>
            <div class="ti-step-lbl">Get Estimate</div>
        </div>
    </div>
</div>

{{-- ======================================================
     STEP 1 – Device Type
====================================================== --}}
<div class="section" id="tiStep1">
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="section-title">
                    <h4 class="title">What type of device do you want to trade in?</h4>
                </div>

                <div class="ti-device-grid">
                    <div class="ti-device-card" data-type="smartphone" onclick="pickDevice(this)">
                        <i class="fa fa-mobile"></i>
                        <div class="ti-device-name">Smartphone</div>
                    </div>
                    <div class="ti-device-card" data-type="ipad" onclick="pickDevice(this)">
                        <i class="fa fa-tablet"></i>
                        <div class="ti-device-name">iPad / Tablet</div>
                    </div>
                    <div class="ti-device-card" data-type="macbook" onclick="pickDevice(this)">
                        <i class="fa fa-laptop"></i>
                        <div class="ti-device-name">MacBook / Laptop</div>
                    </div>
                </div>

                <div style="margin-top:8px;display:flex;justify-content:space-between;align-items:center;">
                    <a href="{{ route('trade_in') }}" class="primary-btn" style="background:#888;border-color:#888;text-decoration:none;">
                        <i class="fa fa-arrow-left"></i> Back to Info
                    </a>
                    <button class="primary-btn" id="btnStep1Next" disabled onclick="goStep(2)">
                        Next <i class="fa fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================
     STEP 2 – Select Model
====================================================== --}}
<div class="section" id="tiStep2" style="display:none;">
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="section-title">
                    <h4 class="title" id="step2Title">Select Your Phone Model</h4>
                </div>

                <div class="input-group" style="margin-bottom:14px;">
                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                    <input type="text" class="form-control" id="modelSearch" placeholder="Search model…">
                </div>

                <div class="ti-model-grid" id="modelGrid"></div>

                <div style="margin-top:24px;display:flex;justify-content:space-between;align-items:center;">
                    <button class="primary-btn" style="background:#888;border-color:#888;" onclick="goStep(1)">
                        <i class="fa fa-arrow-left"></i> Back
                    </button>
                    <button class="primary-btn" id="btnStep2Next" disabled onclick="goStep(3)">
                        Next <i class="fa fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================
     STEP 3 – Assess Condition
====================================================== --}}
<div class="section" id="tiStep3" style="display:none;">
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="section-title">
                    <h4 class="title">Assess Your Device Condition</h4>
                </div>

                {{-- Q1 LCD --}}
                <p class="ti-q-label">LCD / Screen Condition <span style="color:#D10024;">*</span></p>
                <div data-question="lcd">
                    <div class="ti-option" data-points="40" onclick="pick(this)">
                        <div class="ti-option-title">No scratches — Perfect</div>
                        <div class="ti-option-desc">LCD ပြင်သည် အပျက်အစီး မရှိ၊ အပြည့်အဝ ကောင်းမွန်သည်</div>
                    </div>
                    <div class="ti-option" data-points="30" onclick="pick(this)">
                        <div class="ti-option-title">Small scratches</div>
                        <div class="ti-option-desc">LCD ပြင်တွင် အနည်းငယ် ခြစ်ရာများ ရှိသည်</div>
                    </div>
                    <div class="ti-option" data-points="15" onclick="pick(this)">
                        <div class="ti-option-title">Noticeable scratches</div>
                        <div class="ti-option-desc">LCD ပြင်တွင် ထင်ရှားသော ခြစ်ရာများ ရှိသည်</div>
                    </div>
                    <div class="ti-option" data-points="0" onclick="pick(this)">
                        <div class="ti-option-title">Display Issues (cracks / lines / dead pixels)</div>
                        <div class="ti-option-desc">LCD ကွဲနေ / ကိုယ်ကွဲနေ / display message ပေါ်နေသည်</div>
                    </div>
                </div>

                <hr>

                {{-- Q2 Body --}}
                <p class="ti-q-label">Body / Back / Camera Condition <span style="color:#D10024;">*</span></p>
                <div data-question="body">
                    <div class="ti-option" data-points="20" onclick="pick(this)">
                        <div class="ti-option-title">No scratches — Perfect</div>
                        <div class="ti-option-desc">Body, Back glass / cover, Camera lenses အားလုံး ကောင်းမွန်သည်</div>
                    </div>
                    <div class="ti-option" data-points="14" onclick="pick(this)">
                        <div class="ti-option-title">Small scratches</div>
                        <div class="ti-option-desc">Body / Back တွင် အနည်းငယ် ခြစ်ရာများ ရှိသည်</div>
                    </div>
                    <div class="ti-option" data-points="7" onclick="pick(this)">
                        <div class="ti-option-title">Noticeable scratches</div>
                        <div class="ti-option-desc">Body / Back တွင် ထင်ရှားသော ခြစ်ရာများ ရှိသည်</div>
                    </div>
                    <div class="ti-option" data-points="0" onclick="pick(this)">
                        <div class="ti-option-title">Damaged (dents / cracks)</div>
                        <div class="ti-option-desc">Back ကွဲ / Body ကျိုး / Button ပျောက် သို့မဟုတ် ပျက်နေသည်</div>
                    </div>
                </div>

                <hr>

                {{-- Q3 Battery --}}
                <p class="ti-q-label">Battery Health % <span style="color:#D10024;">*</span></p>
                <div style="display:flex;align-items:center;gap:14px;">
                    <input type="number" class="form-control" id="batteryInput" min="1" max="100"
                           placeholder="e.g. 87" style="width:120px;font-weight:700;text-align:center;"
                           oninput="updateBattery(this.value)">
                    <span class="ti-battery-badge" id="batteryBadge">Excellent</span>
                </div>
                <p style="font-size:.85rem;color:#888;margin-top:6px;">Settings → Battery → Battery Health &amp; Charging တွင် စစ်ဆေးပါ</p>

                <hr>

                {{-- Q4 Camera --}}
                <p class="ti-q-label">Camera Test <span style="color:#D10024;">*</span></p>
                <div data-question="camera">
                    <div class="ti-option" data-points="10" onclick="pick(this)">
                        <div class="ti-option-title">Working perfectly</div>
                        <div class="ti-option-desc">ကင်မရာ အပြည့်အဝ ကောင်းမွန်သည်</div>
                    </div>
                    <div class="ti-option" data-points="0" onclick="pick(this)">
                        <div class="ti-option-title">Camera issues (spots / blurry / not opening)</div>
                        <div class="ti-option-desc">ကင်မရာတွင် ပြဿနာ ရှိသည်</div>
                    </div>
                </div>

                <hr>

                {{-- Q5 LCI --}}
                <p class="ti-q-label">Liquid Contact Indicator (LCI) <span style="color:#D10024;">*</span></p>
                <div data-question="lci">
                    <div class="ti-option" data-points="10" onclick="pick(this)">
                        <div class="ti-option-title">White / Silver — No liquid damage</div>
                        <div class="ti-option-desc">ရေမထိနှင့် ဘေးကင်းသောအနေအထား</div>
                    </div>
                    <div class="ti-option" data-points="0" onclick="pick(this)">
                        <div class="ti-option-title">Red / Pink — Liquid damage detected</div>
                        <div class="ti-option-desc">ရေဝင်ဖူး သို့မဟုတ် ချွေးထိနှင့်ဖူးသည်</div>
                    </div>
                </div>

                <hr>

                {{-- Q6 Box --}}
                <p class="ti-q-label">Original Box <span style="color:#D10024;">*</span></p>
                <div data-question="box">
                    <div class="ti-option" data-points="5" onclick="pick(this)">
                        <div class="ti-option-title">Yes — I have the original box</div>
                        <div class="ti-option-desc">မူရင်း box ရှိသည်</div>
                    </div>
                    <div class="ti-option" data-points="0" onclick="pick(this)">
                        <div class="ti-option-title">No — no original box</div>
                        <div class="ti-option-desc">Box မပါ</div>
                    </div>
                </div>

                <hr>

                {{-- Q7 Repair --}}
                <p class="ti-q-label">Repair History <span style="color:#D10024;">*</span></p>
                <div data-question="repair">
                    <div class="ti-option" data-points="10" onclick="pick(this)">
                        <div class="ti-option-title">Never opened / repaired</div>
                        <div class="ti-option-desc">ဘယ်သူမှ မဖွင့်ဖူး၊ မပြင်ဖူးပါ</div>
                    </div>
                    <div class="ti-option" data-points="6" onclick="pick(this)">
                        <div class="ti-option-title">Authorized repair center only</div>
                        <div class="ti-option-desc">ကုမ္ပဏီ ဝန်ဆောင်မှုဌာနမှသာ ပြင်ဆင်ဖူးသည်</div>
                    </div>
                    <div class="ti-option" data-points="0" onclick="pick(this)">
                        <div class="ti-option-title">Third-party / unauthorized repair</div>
                        <div class="ti-option-desc">ပြင်ပဆိုင်မှ ပြင်ဆင်ဖူးသည်</div>
                    </div>
                </div>

                <hr>

                {{-- Q8 Functions --}}
                <p class="ti-q-label">Device Functions <span style="color:#D10024;">*</span></p>
                <div data-question="functions">
                    <div class="ti-option" data-points="5" onclick="pick(this)">
                        <div class="ti-option-title">All functions work perfectly</div>
                        <div class="ti-option-desc">Wi-Fi, Bluetooth, Face ID/Touch ID, Speaker, Mic, Charging — အားလုံး ကောင်းမွန်သည်</div>
                    </div>
                    <div class="ti-option" data-points="0" onclick="pick(this)">
                        <div class="ti-option-title">Some functions have issues</div>
                        <div class="ti-option-desc">တစ်ခုခု အလုပ်မလုပ်ခြင်း ရှိသည်</div>
                    </div>
                </div>

                <div style="margin-top:28px;display:flex;justify-content:space-between;align-items:center;">
                    <button class="primary-btn" style="background:#888;border-color:#888;" onclick="goStep(2)">
                        <i class="fa fa-arrow-left"></i> Back
                    </button>
                    <button class="primary-btn" id="btnStep3Next" disabled onclick="calcAndShow()">
                        Get Estimate <i class="fa fa-arrow-right"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ======================================================
     STEP 4 – Result
====================================================== --}}
<div class="section" id="tiStep4" style="display:none;">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="section-title">
                    <h4 class="title">Your Trade-In Estimate</h4>
                </div>

                <div style="text-align:center;margin-bottom:20px;">
                    <h3 style="margin-bottom:4px;" id="resModel">—</h3>
                    <span style="background:#f5f5f5;border:1px solid #ddd;border-radius:3px;padding:3px 16px;font-size:.9rem;font-weight:700;" id="resStorage">—</span>
                </div>

                <div style="text-align:center;">
                    <span class="ti-grade-badge" id="resGrade">Grade A — Good</span>
                </div>

                <div style="border:1px solid #e5e5e5;border-radius:4px;padding:28px 24px;text-align:center;margin-bottom:20px;">
                    <p style="font-size:.82rem;font-weight:700;text-transform:uppercase;color:#888;letter-spacing:.06em;margin-bottom:4px;">
                        <i class="fa fa-star" style="color:#D10024;"></i> Estimated Trade-In Value
                    </p>
                    <div class="ti-price-val" id="resPrice">— MMK</div>
                    <p style="font-size:.82rem;color:#888;margin-bottom:14px;">Price range in MMK</p>
                    <span style="font-size:.85rem;color:#888;margin-right:14px;">
                        <i class="fa fa-check-circle" style="color:#D10024;"></i> Competitive Price
                    </span>
                    <span style="font-size:.85rem;color:#888;">
                        <i class="fa fa-check-circle" style="color:#D10024;"></i> Market Value
                    </span>
                </div>

                <div class="ti-notice">
                    <strong><i class="fa fa-info-circle"></i> သတိပြုရန်</strong><br>
                    ဤစျေးနှုန်းသည် device condition grade ပေါ် မူတည်၍ ခန့်မှန်းထားသည်သာ ဖြစ်သည်။
                    တကယ်ဝယ်ယူမည့် စျေးနှုန်းကို ကျွန်ုပ်တို့ဆိုင်တွင် စစ်ဆေးပြီးမှ နောက်ဆုံး ဆုံးဖြတ်ပါမည်။
                </div>

                <div style="margin-top:24px;display:flex;justify-content:space-between;align-items:center;">
                    <button class="primary-btn" style="background:#888;border-color:#888;" onclick="goStep(3)">
                        <i class="fa fa-arrow-left"></i> Back
                    </button>
                    <button class="primary-btn" onclick="restartAll()">
                        <i class="fa fa-refresh"></i> Start Over
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
/* ===================================================
   MODEL DATA — grouped by device type
=================================================== */
var allModels = {

    smartphone: [
        /* ---- iPhones ---- */
        { n:'iPhone 11',        s:'64GB',   hi:700000,   lo:580000  },
        { n:'iPhone 11',        s:'128GB',  hi:800000,   lo:660000  },
        { n:'iPhone 11',        s:'256GB',  hi:920000,   lo:760000  },
        { n:'iPhone 11 Pro',    s:'64GB',   hi:900000,   lo:740000  },
        { n:'iPhone 11 Pro',    s:'256GB',  hi:1050000,  lo:860000  },
        { n:'iPhone 11 Pro',    s:'512GB',  hi:1200000,  lo:980000  },
        { n:'iPhone 11 Pro Max',s:'64GB',   hi:980000,   lo:800000  },
        { n:'iPhone 11 Pro Max',s:'256GB',  hi:1130000,  lo:920000  },
        { n:'iPhone 11 Pro Max',s:'512GB',  hi:1280000,  lo:1050000 },
        { n:'iPhone 12',        s:'64GB',   hi:1000000,  lo:820000  },
        { n:'iPhone 12',        s:'128GB',  hi:1120000,  lo:920000  },
        { n:'iPhone 12',        s:'256GB',  hi:1250000,  lo:1020000 },
        { n:'iPhone 12 mini',   s:'64GB',   hi:850000,   lo:690000  },
        { n:'iPhone 12 mini',   s:'128GB',  hi:950000,   lo:780000  },
        { n:'iPhone 12 mini',   s:'256GB',  hi:1070000,  lo:870000  },
        { n:'iPhone 12 Pro',    s:'128GB',  hi:1300000,  lo:1060000 },
        { n:'iPhone 12 Pro',    s:'256GB',  hi:1440000,  lo:1180000 },
        { n:'iPhone 12 Pro',    s:'512GB',  hi:1600000,  lo:1300000 },
        { n:'iPhone 12 Pro Max',s:'128GB',  hi:1420000,  lo:1160000 },
        { n:'iPhone 12 Pro Max',s:'256GB',  hi:1560000,  lo:1280000 },
        { n:'iPhone 12 Pro Max',s:'512GB',  hi:1720000,  lo:1400000 },
        { n:'iPhone 13',        s:'128GB',  hi:1550000,  lo:1280000 },
        { n:'iPhone 13',        s:'256GB',  hi:1700000,  lo:1400000 },
        { n:'iPhone 13',        s:'512GB',  hi:1900000,  lo:1560000 },
        { n:'iPhone 13 mini',   s:'128GB',  hi:1250000,  lo:1030000 },
        { n:'iPhone 13 mini',   s:'256GB',  hi:1400000,  lo:1150000 },
        { n:'iPhone 13 Pro',    s:'128GB',  hi:1850000,  lo:1520000 },
        { n:'iPhone 13 Pro',    s:'256GB',  hi:2050000,  lo:1680000 },
        { n:'iPhone 13 Pro',    s:'512GB',  hi:2280000,  lo:1870000 },
        { n:'iPhone 13 Pro Max',s:'128GB',  hi:2000000,  lo:1640000 },
        { n:'iPhone 13 Pro Max',s:'256GB',  hi:2200000,  lo:1800000 },
        { n:'iPhone 13 Pro Max',s:'512GB',  hi:2450000,  lo:2000000 },
        { n:'iPhone 14',        s:'128GB',  hi:2050000,  lo:1680000 },
        { n:'iPhone 14',        s:'256GB',  hi:2250000,  lo:1840000 },
        { n:'iPhone 14 Plus',   s:'128GB',  hi:2150000,  lo:1760000 },
        { n:'iPhone 14 Plus',   s:'256GB',  hi:2380000,  lo:1950000 },
        { n:'iPhone 14 Pro',    s:'128GB',  hi:2500000,  lo:2050000 },
        { n:'iPhone 14 Pro',    s:'256GB',  hi:2750000,  lo:2250000 },
        { n:'iPhone 14 Pro Max',s:'128GB',  hi:2680000,  lo:2200000 },
        { n:'iPhone 14 Pro Max',s:'256GB',  hi:2950000,  lo:2420000 },
        { n:'iPhone 15',        s:'128GB',  hi:2700000,  lo:2200000 },
        { n:'iPhone 15',        s:'256GB',  hi:2980000,  lo:2440000 },
        { n:'iPhone 15 Plus',   s:'128GB',  hi:2850000,  lo:2320000 },
        { n:'iPhone 15 Pro',    s:'128GB',  hi:3200000,  lo:2600000 },
        { n:'iPhone 15 Pro',    s:'256GB',  hi:3550000,  lo:2900000 },
        { n:'iPhone 15 Pro Max',s:'256GB',  hi:3800000,  lo:3100000 },
        { n:'iPhone 15 Pro Max',s:'512GB',  hi:4200000,  lo:3450000 },
        /* ---- Samsung ---- */
        { n:'Samsung S22',      s:'128GB',  hi:1150000,  lo:940000  },
        { n:'Samsung S22 Ultra',s:'128GB',  hi:1500000,  lo:1230000 },
        { n:'Samsung S23',      s:'128GB',  hi:1400000,  lo:1150000 },
        { n:'Samsung S23 Ultra',s:'256GB',  hi:1950000,  lo:1600000 },
        { n:'Samsung S24',      s:'128GB',  hi:1700000,  lo:1400000 },
        { n:'Samsung S24 Ultra',s:'256GB',  hi:2350000,  lo:1920000 },
        { n:'Samsung A54',      s:'128GB',  hi:750000,   lo:610000  },
        { n:'Samsung A34',      s:'128GB',  hi:600000,   lo:480000  },
        { n:'Samsung A73',      s:'128GB',  hi:820000,   lo:670000  },
        { n:'Samsung A14',      s:'128GB',  hi:420000,   lo:340000  },
    ],

    ipad: [
        { n:'iPad (9th Gen)',       s:'64GB  Wi-Fi',      hi:700000,  lo:580000  },
        { n:'iPad (9th Gen)',       s:'256GB Wi-Fi',      hi:850000,  lo:700000  },
        { n:'iPad (10th Gen)',      s:'64GB  Wi-Fi',      hi:900000,  lo:740000  },
        { n:'iPad (10th Gen)',      s:'256GB Wi-Fi',      hi:1080000, lo:880000  },
        { n:'iPad mini 5',         s:'64GB  Wi-Fi',      hi:720000,  lo:590000  },
        { n:'iPad mini 6',         s:'64GB  Wi-Fi',      hi:1000000, lo:820000  },
        { n:'iPad mini 6',         s:'256GB Wi-Fi',      hi:1200000, lo:980000  },
        { n:'iPad Air 4',          s:'64GB  Wi-Fi',      hi:1100000, lo:900000  },
        { n:'iPad Air 5',          s:'64GB  Wi-Fi',      hi:1350000, lo:1100000 },
        { n:'iPad Air 5',          s:'256GB Wi-Fi',      hi:1600000, lo:1300000 },
        { n:'iPad Pro 11" M1',     s:'128GB Wi-Fi',      hi:1600000, lo:1300000 },
        { n:'iPad Pro 11" M1',     s:'256GB Wi-Fi',      hi:1850000, lo:1520000 },
        { n:'iPad Pro 11" M2',     s:'128GB Wi-Fi',      hi:1900000, lo:1560000 },
        { n:'iPad Pro 11" M2',     s:'256GB Wi-Fi',      hi:2200000, lo:1800000 },
        { n:'iPad Pro 12.9" M1',   s:'128GB Wi-Fi',      hi:2000000, lo:1640000 },
        { n:'iPad Pro 12.9" M2',   s:'128GB Wi-Fi',      hi:2400000, lo:1960000 },
        { n:'iPad Pro 12.9" M2',   s:'256GB Wi-Fi',      hi:2750000, lo:2250000 },
        { n:'Samsung Tab S8',      s:'128GB',            hi:950000,  lo:780000  },
        { n:'Samsung Tab S8 Ultra',s:'128GB',            hi:1350000, lo:1100000 },
        { n:'Samsung Tab S9',      s:'128GB',            hi:1200000, lo:980000  },
        { n:'Samsung Tab S9 Ultra',s:'256GB',            hi:1900000, lo:1560000 },
    ],

    macbook: [
        { n:'MacBook Air M1',      s:'8GB / 256GB',  hi:2200000,  lo:1800000 },
        { n:'MacBook Air M1',      s:'8GB / 512GB',  hi:2550000,  lo:2100000 },
        { n:'MacBook Air M2',      s:'8GB / 256GB',  hi:2800000,  lo:2300000 },
        { n:'MacBook Air M2',      s:'8GB / 512GB',  hi:3200000,  lo:2600000 },
        { n:'MacBook Air M2',      s:'16GB / 512GB', hi:3700000,  lo:3050000 },
        { n:'MacBook Air M3',      s:'8GB / 256GB',  hi:3300000,  lo:2700000 },
        { n:'MacBook Air M3',      s:'16GB / 512GB', hi:4200000,  lo:3450000 },
        { n:'MacBook Pro 13" M1',  s:'8GB / 256GB',  hi:2600000,  lo:2130000 },
        { n:'MacBook Pro 13" M2',  s:'8GB / 256GB',  hi:3000000,  lo:2450000 },
        { n:'MacBook Pro 14" M1 Pro',s:'16GB / 512GB',hi:4000000, lo:3300000 },
        { n:'MacBook Pro 14" M3 Pro',s:'18GB / 512GB',hi:5000000, lo:4100000 },
        { n:'MacBook Pro 16" M1 Pro',s:'16GB / 512GB',hi:4800000, lo:3950000 },
        { n:'MacBook Pro 16" M3 Max',s:'36GB / 1TB',  hi:7500000, lo:6200000 },
        { n:'Dell XPS 13',         s:'16GB / 512GB', hi:1800000,  lo:1480000 },
        { n:'Dell XPS 15',         s:'16GB / 512GB', hi:2200000,  lo:1800000 },
        { n:'HP Spectre x360',     s:'16GB / 512GB', hi:1600000,  lo:1300000 },
        { n:'Lenovo ThinkPad X1',  s:'16GB / 512GB', hi:1750000,  lo:1430000 },
        { n:'ASUS ZenBook 14',     s:'16GB / 512GB', hi:1200000,  lo:980000  },
    ],
};

/* ===================================================
   STATE
=================================================== */
var selType = '';
var selIdx  = -1;
var models  = [];
var batteryPts = 5;
var QUESTIONS = ['lcd','body','camera','lci','box','repair','functions'];

/* ===================================================
   STEP 1 – Device type selection
=================================================== */
function pickDevice(el) {
    document.querySelectorAll('.ti-device-card').forEach(function(c){ c.classList.remove('selected'); });
    el.classList.add('selected');
    selType = el.getAttribute('data-type');
    selIdx  = -1;
    document.getElementById('btnStep1Next').disabled = false;

    // Update step 2 title
    var titles = { smartphone:'Select Your Phone Model', ipad:'Select Your iPad / Tablet Model', macbook:'Select Your MacBook / Laptop Model' };
    document.getElementById('step2Title').textContent = titles[selType] || 'Select Model';
}

/* ===================================================
   STEP 2 – Model grid
=================================================== */
function renderGrid(filter) {
    models = allModels[selType] || [];
    var grid = document.getElementById('modelGrid');
    grid.innerHTML = '';
    var lc = (filter||'').toLowerCase();
    models.forEach(function(m, i) {
        var lbl = m.n + ' ' + m.s;
        if (lc && lbl.toLowerCase().indexOf(lc) < 0) return;
        var btn = document.createElement('button');
        btn.className = 'ti-model-btn' + (selIdx===i ? ' selected' : '');
        btn.textContent = lbl;
        btn.setAttribute('data-i', i);
        btn.onclick = function() {
            selIdx = i;
            document.querySelectorAll('.ti-model-btn').forEach(function(b){ b.classList.remove('selected'); });
            btn.classList.add('selected');
            document.getElementById('btnStep2Next').disabled = false;
        };
        grid.appendChild(btn);
    });
    if (!grid.children.length) {
        grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;color:#888;padding:20px;">No models found.</div>';
    }
}

/* ===================================================
   STEP 3 – Condition options
=================================================== */
function pick(el) {
    el.parentElement.querySelectorAll('.ti-option').forEach(function(o){ o.classList.remove('selected'); });
    el.classList.add('selected');
    checkStep3();
}

function checkStep3() {
    var bval = parseInt(document.getElementById('batteryInput').value) || 0;
    var ok = QUESTIONS.every(function(q){
        return document.querySelector('[data-question="'+q+'"] .ti-option.selected');
    }) && bval >= 1 && bval <= 100;
    document.getElementById('btnStep3Next').disabled = !ok;
}

function updateBattery(v) {
    v = parseInt(v)||0;
    var badge = document.getElementById('batteryBadge');
    if (v >= 85)      { badge.textContent='Excellent'; badge.className='ti-battery-badge'; batteryPts=5; }
    else if (v >= 70) { badge.textContent='Good';      badge.className='ti-battery-badge'; batteryPts=3; }
    else if (v >= 50) { badge.textContent='Fair';      badge.className='ti-battery-badge fair'; batteryPts=1; }
    else              { badge.textContent='Poor';      badge.className='ti-battery-badge poor'; batteryPts=0; }
    checkStep3();
}

/* ===================================================
   STEP 4 – Calculate & show result
=================================================== */
function calcAndShow() {
    var MAX = 40+20+5+10+10+5+10+5; // 105
    var pts = batteryPts;
    QUESTIONS.forEach(function(q){
        var sel = document.querySelector('[data-question="'+q+'"] .ti-option.selected');
        if (sel) pts += parseInt(sel.getAttribute('data-points')||0);
    });
    var pct = pts / MAX;

    var gc, gl;
    if      (pct >= 0.88) { gc='grade-S'; gl='Grade S — Like New'; }
    else if (pct >= 0.70) { gc='grade-A'; gl='Grade A — Good Condition'; }
    else if (pct >= 0.50) { gc='grade-B'; gl='Grade B — Fair Condition'; }
    else                  { gc='grade-C'; gl='Grade C — Poor Condition'; }

    var m  = models[selIdx];
    var lo = Math.round(m.lo * pct / 10000) * 10000;
    var hi = Math.round(m.hi * pct / 10000) * 10000;
    if (lo < 100000) lo = 100000;
    if (hi < lo)     hi = lo + 100000;

    document.getElementById('resModel').textContent   = m.n;
    document.getElementById('resStorage').textContent = m.s;
    document.getElementById('resPrice').textContent   = fmt(lo) + ' – ' + fmt(hi) + ' MMK';
    var gb = document.getElementById('resGrade');
    gb.className   = 'ti-grade-badge ' + gc;
    gb.textContent = gl;

    goStep(4);
}

/* ===================================================
   NAVIGATION (4 steps)
=================================================== */
function goStep(n) {
    document.getElementById('tiStep1').style.display = n===1 ? '' : 'none';
    document.getElementById('tiStep2').style.display = n===2 ? '' : 'none';
    document.getElementById('tiStep3').style.display = n===3 ? '' : 'none';
    document.getElementById('tiStep4').style.display = n===4 ? '' : 'none';

    if (n===2) {
        renderGrid('');
        document.getElementById('modelSearch').value = '';
        document.getElementById('btnStep2Next').disabled = selIdx < 0;
    }

    for (var i=1; i<=4; i++) {
        var dot = document.getElementById('sdot'+i);
        var num = document.getElementById('snum'+i);
        dot.className = 'ti-step-item' + (i===n ? ' active' : (i<n ? ' done' : ''));
        num.innerHTML = i < n ? '<i class="fa fa-check"></i>' : i;
    }
    document.getElementById('conn1').className = 'ti-connector' + (n>1 ? ' done' : '');
    document.getElementById('conn2').className = 'ti-connector' + (n>2 ? ' done' : '');
    document.getElementById('conn3').className = 'ti-connector' + (n>3 ? ' done' : '');

    window.scrollTo({top:0, behavior:'smooth'});
}

function restartAll() {
    selType=''; selIdx=-1; batteryPts=5;
    document.querySelectorAll('.ti-device-card').forEach(function(c){ c.classList.remove('selected'); });
    document.querySelectorAll('.ti-option').forEach(function(o){ o.classList.remove('selected'); });
    document.getElementById('batteryInput').value='';
    document.getElementById('batteryBadge').textContent='Excellent';
    document.getElementById('batteryBadge').className='ti-battery-badge';
    document.getElementById('btnStep1Next').disabled=true;
    document.getElementById('btnStep2Next').disabled=true;
    document.getElementById('btnStep3Next').disabled=true;
    goStep(1);
}

function fmt(n){ return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g,','); }

/* ===================================================
   INIT
=================================================== */
$(document).ready(function(){
    $('#modelSearch').on('input', function(){
        renderGrid(this.value);
        if (selIdx !== -1) {
            var b = document.querySelector('.ti-model-btn[data-i="'+selIdx+'"]');
            if (b) b.classList.add('selected');
        }
    });
});
</script>
@endsection
