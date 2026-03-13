@extends('layouts.user')
@section('title', 'Device Trade-In')

@section('style')
<style>
    .ti-terms-wrapper{
    padding:60px 0 20px;
    background:#fff;
    }

    .ti-terms-card{
        background:#f3f3f3;
        border-radius:20px;
        padding:40px 50px;
        box-shadow:0 3px 10px rgba(0,0,0,0.05);
    }

    .ti-terms-card h2{
        font-weight:700;
        margin-bottom:25px;
        color:#333;
    }

    .ti-terms-list{
        padding-left:20px;
    }

    .ti-terms-list li{
        margin-bottom:12px;
        font-size:16px;
        line-height:1.6;
    }

    @media(max-width:768px){
        .ti-terms-card{
            padding:30px;
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
        margin-bottom: 15px;
    }
    .ti-info-hero p {
        font-size: 1.2rem;
        color: #888;
        max-width: 700px;
        margin: 0 auto 30px;
    }
    .ti-steps-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-top: 50px;
    }
    .ti-step-card {
        text-align: center;
        padding: 30px;
        background: #fff;
        border: 1px solid #eee;
        border-radius: 8px;
        transition: transform 0.3s ease;
    }
    .ti-step-card:hover { border-color: #D10024; }
    .ti-step-icon {
        width: 60px;
        height: 60px;
        background: #D10024;
        color: #fff;
        font-size: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    .ti-step-card h4 { font-weight: 700; margin-bottom: 10px; }
    .ti-policy-section {
        background: #fff;
        padding: 60px 0;
    }
    .ti-policy-box {
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 40px;
        margin-bottom: 30px;
    }
    .ti-policy-box h3 {
        font-weight: 700;
        color: #D10024;
        margin-bottom: 25px;
        border-bottom: 2px solid #D10024;
        display: inline-block;
        padding-bottom: 5px;
    }
    .ti-policy-list {
        list-style: none;
        padding: 0;
    }
    .ti-policy-list li {
        margin-bottom: 15px;
        position: relative;
        padding-left: 25px;
        font-size: 1.1rem;
        line-height: 1.6;
    }
    .ti-policy-list li:before {
        content: "\f058";
        font-family: FontAwesome;
        position: absolute;
        left: 0;
        color: #D10024;
    }
    .ti-cta-btn {
        display: inline-block;
        padding: 15px 40px;
        background-color: #D10024;
        color: #FFF;
        font-weight: 700;
        border: none;
        border-radius: 40px;
        text-transform: uppercase;
        transition: all 0.2s;
        font-size: 1.2rem;
        text-decoration: none;
    }
    .ti-cta-btn:hover {
        background-color: #E60026;
        color: #FFF;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(209,0,36,0.3);
    }
    @media (max-width: 768px) {
        .ti-steps-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="ti-info-hero">
    <div class="container">
        <h1>Trade-In your device for a better value</h1>
        <p>Get a competitive value for your current smartphone, tablet, or laptop and use it towards your next purchase at Flexicell.</p>
        <a href="{{ route('trade_in.estimate') }}" class="ti-cta-btn">Get Started &nbsp; <i class="fa fa-chevron-right"></i></a>

        <div class="ti-steps-grid">
            <div class="ti-step-card">
                <div class="ti-step-icon"><i class="fa fa-search"></i></div>
                <h4>1. Select Your Device</h4>
                <p>Choose your brand and model from our extensive database.</p>
            </div>
            <div class="ti-step-card">
                <div class="ti-step-icon"><i class="fa fa-check-square-o"></i></div>
                <h4>2. Assess Condition</h4>
                <p>Answer a few simple questions about your device's state.</p>
            </div>
            <div class="ti-step-card">
                <div class="ti-step-icon"><i class="fa fa-money"></i></div>
                <h4>3. Get Paid</h4>
                <p>Receive an instant estimate and visit us to finalize the trade.</p>
            </div>
        </div>
    </div>
</div>

<div class="ti-terms-wrapper">
    <div class="container">
        <div class="ti-terms-card">

            <h2>Trade-in Terms & Conditions</h2>

            <ul class="ti-terms-list">
                <li>Trade-in Service ကို iPhone 11 Series မှ စပြီး လက်ခံဆောင်ရွက်ပေးပါသည်။</li>

                <li>Trade-in Service အတွက် Old Device အဖြစ် iPhone 11 Series မှ iPhone 16 Series ထိ လက်ခံပါသည်။</li>

                <li>iPhone 17 Family နှင့် iPhone Air များကို Trade-in Service တွင် မပါဝင်ပါ။</li>

                <li>Brand New Packing နှင့် Non-activated XtraSure / iSure device များကို Trade-in မပြုလုပ်နိုင်ပါ။</li>

                <li>Trade-in ပြုလုပ်ရာတွင် Trade Up နှင့် Trade Down နှစ်မျိုးလုံး ရရှိနိုင်ပါသည်။</li>

                <li>iPhone 13 Family မှ စပြီး LL/A Region Device များကို Trade-in လက်ခံပါသည်။</li>

                <li>iPhone 15 Pro / Pro Max ၊ iPhone 16 Family မှာ CH/A Region များကို လက်ခံပါသည်။</li>

                <li>Dubai Region (AE) Device နှင့် Europe Region များကို လက်ခံပါသည်။</li>

                <li>Refurbished Device ၊ Unknown Part ပါသော Device များကို လက်မခံပါ။</li>

                <li>Battery Service ပြုလုပ်ထားသော Device များတွင် Battery Service Charges ကို ထပ်မံတွက်ချက်ပါမည်။</li>

                <li>Apple Official Repair / Replacement Genuine Part Device များကို လက်ခံပါသည်။</li>

                <li>Trade-in ပြုလုပ်မည့် Device များတွင် Error ၊ Data များရှိပါက Device ကို Erase ပြုလုပ်ပေးရပါမည်။</li>

                <li>Reset ပြုလုပ်ပြီးနောက် LCD Green Screen ၊ Camera Spot စသော Error များရှိပါက လက်မခံနိုင်ပါ။</li>

                <li>Error များရှိသော Device များကို Trade-in Service မှာ လက်ခံမည် မဟုတ်ပါ။</li>

                <li>Trade-in Device များတွင် Data များကို Reset ပြုလုပ်ပေးရန် Customer မှ တာဝန်ယူရပါမည်။</li>
            </ul>

        </div>
    </div>
</div>

<div class="ti-policy-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-title">
                    <h2 class="title" style="font-weight: 700">Trade-In Guidelines</h2>
                </div>
            </div>

            <div class="col-md-6">
                <div class="ti-policy-box">
                    <h3>Smartphones</h3>
                    <ul class="ti-policy-list">
                        <li>The device must power on and hold a charge.</li>
                        <li>The screen must be functional and free of severe cracks.</li>
                        <li>Face ID, Touch ID, and all camera functions should work.</li>
                        <li>Original box and accessories will increase the value.</li>
                        <li>Must be factory reset with Apple ID or Google account signed out.</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6">
                <div class="ti-policy-box">
                    <h3>Tablets & iPads</h3>
                    <ul class="ti-policy-list">
                        <li>LCD must be free of lines, dead pixels, or significant discoloration.</li>
                        <li>Wi-Fi and Bluetooth connectivity must be stable.</li>
                        <li>Physical buttons (Power, Volume) must be responsive.</li>
                        <li>No liquid damage (LCI must be white/silver).</li>
                        <li>Body should be free of severe dents or bending.</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-12">
                <div class="ti-policy-box">
                    <h3>MacBooks & Laptops</h3>
                    <ul class="ti-policy-list">
                        <li>Keyboard must be fully functional (all keys working).</li>
                        <li>Trackpad, Touch Bar, and Ports must operate correctly.</li>
                        <li>Battery cycle count should be within reasonable limits.</li>
                        <li>Screen hinge must be firm and display coating (StainGate) intact.</li>
                        <li>Logic board must be free of repairs or water damage.</li>
                        <li>iCloud / Find My Mac must be turned off.</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-12 text-center" style="margin-top: 30px;">
                <p style="color: #888; font-style: italic; margin-bottom: 20px;">
                    * Final evaluation will be conducted at our Flexicell retail stores by our technical team.
                </p>
                <a href="{{ route('trade_in.estimate') }}" class="ti-cta-btn">Estimate My Device Now</a>
            </div>
        </div>
    </div>
</div>
@endsection
