@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<style>
    body, .ao-panel, .ao-panel-head h2, .ao-btn, .ao-status-select, .ao-save-btn, .ao-item-name, .ao-item-variant, .ao-customer-name, .ao-customer-sub, .ao-info-label, .ao-info-value, .ao-note-box, .ao-topbar-meta, .ao-back-link, .ao-paid-tag, .ao-unpaid-tag, .ao-pay-name, .ao-pay-sub, .ao-count-tag {
        font-family: 'Inter', sans-serif !important;
    }
    .ao-mono, table.ao-items td, table.ao-items th.num, .ao-topbar h1 {
        font-family: 'JetBrains Mono', monospace !important;
    }
    :root{
        --bg:#EEF1F4;
        --card:#FFFFFF;
        --ink:#1C2024;
        --ink-soft:#7A828C;
        --border:#ECEEF1;
        --red:#A31E22;
        --red-dark:#5C0E12;
        --red-tint:#FBEAEA;
        --purple:#6C4EE8;
        --purple-tint:#F1EEFE;
        --green:#12B76A;
        --green-tint:#E6F8EE;
        --amber:#F5A623;
        --amber-tint:#FDF1D9;
        --blue:#3B82F6;
        --blue-tint:#EAF2FE;
        --peach:#FBEFE3;
        --radius:16px;
        --shadow:0 1px 2px rgba(20,20,30,0.03), 0 10px 24px -14px rgba(20,20,30,0.10);
    }
    .ao-mono{font-family:'JetBrains Mono', monospace;}

    .ao-topbar{display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:18px;}
    .ao-topbar-left{display:flex; flex-direction:column; gap:6px;}
    .ao-back-link{display:flex; align-items:center; gap:5px; font-size:12.5px; color:var(--ink-soft); font-weight:500; width:fit-content;}
    .ao-back-link:hover{color:var(--ink);}
    .ao-title-row{display:flex; align-items:center; gap:12px;}
    .ao-topbar h1{font-size:20px; font-weight:700; margin:0; font-family:'JetBrains Mono', monospace;}
    .ao-status-badge{font-size:11.5px; font-weight:700; padding:5px 12px; border-radius:999px; text-transform:capitalize;}
    .ao-status-badge.delivered{background:var(--green-tint); color:var(--green);}
    .ao-status-badge.pending{background:var(--amber-tint); color:var(--amber);}
    .ao-status-badge.processing{background:var(--blue-tint); color:var(--blue);}
    .ao-status-badge.shipped{background:var(--purple-tint); color:var(--purple);}
    .ao-status-badge.cancelled{background:var(--red-tint); color:var(--red);}
    .ao-status-badge.returned{background:var(--border); color:var(--ink-soft);}
    .ao-topbar-meta{font-size:12px; color:var(--ink-soft);}
    .ao-topbar-actions{display:flex; gap:10px;}
    .ao-btn{display:flex; align-items:center; gap:7px; font-size:13px; font-weight:600; padding:10px 16px; border-radius:10px; cursor:pointer; border:1.5px solid var(--border); background:#fff; color:var(--ink); transition:background .15s ease, border-color .15s ease; text-decoration:none;}
    .ao-btn:hover{background:#F7F5F3;}
    .ao-btn.primary{background:var(--red); border-color:var(--red); color:#fff;}
    .ao-btn.primary:hover{background:var(--red-dark); border-color:var(--red-dark);}

    .ao-panel{background:var(--card); border-radius:var(--radius); box-shadow:var(--shadow); padding:20px 24px;}

    .ao-stepper{display:flex; align-items:center;}
    .ao-step{display:flex; flex-direction:column; align-items:center; gap:8px; position:relative; flex:1;}
    .ao-step-dot{width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; background:var(--border); color:var(--ink-soft); font-size:13px; font-weight:700; z-index:1; border:3px solid var(--card);}
    .ao-step.done .ao-step-dot{background:var(--green); color:#fff;}
    .ao-step.current .ao-step-dot{background:var(--red); color:#fff; box-shadow:0 0 0 4px var(--red-tint);}
    .ao-step-label{font-size:12px; font-weight:600; color:var(--ink-soft); text-align:center;}
    .ao-step.done .ao-step-label, .ao-step.current .ao-step-label{color:var(--ink);}
    .ao-step-time{font-size:10.5px; color:var(--ink-soft);}
    .ao-step-line{position:absolute; top:16px; left:50%; width:100%; height:2px; background:var(--border); z-index:0;}
    .ao-step:last-child .ao-step-line{display:none;}
    .ao-step.done .ao-step-line{background:var(--green);}

    .ao-content-grid{display:grid; grid-template-columns:1fr 320px; gap:18px; align-items:start;}
    .ao-col-left, .ao-col-right{display:flex; flex-direction:column; gap:18px;}

    .ao-panel-head{display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;}
    .ao-panel-head h2{font-size:15.5px; font-weight:700; margin:0;}
    .ao-count-tag{font-size:11.5px; color:var(--ink-soft); background:var(--bg); padding:3px 10px; border-radius:999px;}

    table.ao-items{width:100%; border-collapse:collapse;}
    table.ao-items th{text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:0.04em; color:var(--ink-soft); padding:0 8px 10px; font-weight:600;}
    table.ao-items th.num, table.ao-items td.num{text-align:right;}
    table.ao-items td{padding:12px 8px; border-top:1px solid var(--border); vertical-align:middle;}
    .ao-item-cell{display:flex; align-items:center; gap:12px;}
    .ao-item-thumb{width:44px; height:44px; border-radius:9px; flex-shrink:0; background:linear-gradient(135deg, var(--purple-tint), #fff); display:flex; align-items:center; justify-content:center; font-weight:700; color:var(--purple); font-size:15px; overflow:hidden;}
    .ao-item-thumb img{width:100%; height:100%; object-fit:cover;}
    .ao-item-name{font-size:13.5px; font-weight:600;}
    .ao-item-variant{font-size:11.5px; color:var(--ink-soft); margin-top:2px;}

    .ao-totals-block{margin-top:6px; padding-top:14px; border-top:1px solid var(--border);}
    .ao-total-row{display:flex; justify-content:space-between; font-size:13px; color:var(--ink-soft); padding:5px 8px;}
    .ao-total-row.discount span:last-child{color:var(--green);}
    .ao-total-row.grand{font-size:15.5px; font-weight:700; color:var(--ink); padding-top:10px; margin-top:4px; border-top:1px dashed var(--border);}
    .ao-total-row.grand span:last-child{color:var(--red);}

    .ao-note-box{background:var(--amber-tint); border:1px dashed var(--amber); border-radius:10px; padding:12px 14px; font-size:12.5px; color:#8A5A0B; display:flex; gap:8px; align-items:flex-start;}
    .ao-internal-note-box{background:#f3f0ff; border:1px dashed #7c3aed; border-radius:10px; padding:12px 14px; font-size:12.5px; color:#5b21b6; display:flex; gap:8px; align-items:flex-start;}

    .ao-info-card .ao-info-row{display:flex; align-items:flex-start; gap:10px; padding:9px 0; font-size:13px;}
    .ao-info-card .ao-info-row svg{color:var(--ink-soft); margin-top:1px; flex-shrink:0;}
    .ao-info-label{font-size:11px; color:var(--ink-soft); margin-bottom:2px;}
    .ao-info-value{font-weight:600;}
    .ao-customer-head{display:flex; align-items:center; gap:12px; margin-bottom:6px;}
    .ao-avatar{width:42px; height:42px; border-radius:50%; background:var(--red); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:15px; flex-shrink:0;}
    .ao-customer-name{font-size:14px; font-weight:700;}
    .ao-customer-name-row{display:flex; align-items:center; gap:8px; flex-wrap:wrap;}
    .ao-view-profile{display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:600; padding:4px 10px; border-radius:7px; border:1.5px solid var(--border); background:#fff; color:var(--ink); text-decoration:none; white-space:nowrap; transition:background .15s ease, color .15s ease, border-color .15s ease;}
    .ao-view-profile:hover{background:var(--blue); border-color:var(--blue); color:#fff;}
    .ao-customer-sub{font-size:11.5px; color:var(--ink-soft);}
    .ao-divider{height:1px; background:var(--border); margin:12px 0;}

    .ao-pay-row{display:flex; align-items:center; gap:10px; padding:10px 12px; background:var(--bg); border-radius:10px; margin-bottom:10px;}
    .ao-pay-badge{width:34px; height:24px; border-radius:5px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:9.5px; font-weight:700; color:#fff;}
    .ao-bkash{background:#E2136E;}
    .ao-nagad{background:#F6921E;}
    .ao-rocket{background:#E2136E;}
    .ao-cod-badge{background:var(--amber); color:#fff;}
    .ao-card-badge{background:var(--blue); color:#fff;}
    .ao-pay-name{font-size:12.5px; font-weight:600;}
    .ao-pay-sub{font-size:10.5px; color:var(--ink-soft);}
    .ao-paid-tag{margin-left:auto; font-size:10.5px; font-weight:700; color:var(--green); background:var(--green-tint); padding:3px 9px; border-radius:999px;}
    .ao-unpaid-tag{margin-left:auto; font-size:10.5px; font-weight:700; color:var(--red); background:var(--red-tint); padding:3px 9px; border-radius:999px;}

    .ao-status-select{width:100%; padding:10px 12px; border-radius:9px; border:1.5px solid var(--border); font-size:13px; font-family:'Inter',sans-serif; color:var(--ink); background:#fff; margin-bottom:10px;}
    .ao-save-btn{width:100%; background:var(--red); color:#fff; border:none; border-radius:9px; padding:11px; font-size:13px; font-weight:600; cursor:pointer; font-family:'Inter',sans-serif;}
    .ao-save-btn:hover{background:var(--red-dark);}

    .ao-cod-note{margin-top:12px; padding:10px 12px; background:var(--amber-tint); border-radius:8px; border:1px solid rgba(245,166,35,0.2); display:flex; gap:8px; align-items:flex-start;}
    .ao-cod-note svg{color:var(--amber); flex-shrink:0; margin-top:1px;}
    .ao-cod-note p{font-size:11.5px; color:#8A5A0B; margin:0;}
    .ao-cod-note p strong{font-weight:700;}

    .ao-push-btn{width:100%; display:flex; align-items:center; justify-content:center; gap:8px; background:#32A386; color:#fff; border:none; border-radius:9px; padding:11px; font-size:13px; font-weight:600; cursor:pointer; font-family:'Inter',sans-serif; transition:opacity .15s ease;}
    .ao-push-btn:hover{opacity:0.9;}
    .ao-push-btn:disabled{opacity:0.5; cursor:not-allowed;}
    .ao-push-btn svg{flex-shrink:0;}
    .ao-push-btn.pathao{background:#ED1C24;}
    .ao-push-btn.pathao:hover{background:#c4171d;}
    .ao-push-btn.redx{background:#6C4EE8;}
    .ao-push-btn.redx:hover{background:#5a3fd4;}
    .ao-consignment-box{margin-top:12px; padding:12px 14px; background:var(--green-tint); border:1px solid rgba(18,183,106,0.2); border-radius:10px; display:flex; gap:8px; align-items:flex-start;}
    .ao-consignment-box svg{color:var(--green); flex-shrink:0; margin-top:1px;}
    .ao-consignment-box .ao-cc-label{font-size:11px; color:var(--ink-soft); margin-bottom:2px;}
    .ao-consignment-box .ao-cc-value{font-weight:600; font-size:13px; color:var(--ink);}
    .ao-consignment-box .ao-cc-sub{font-size:10.5px; color:var(--ink-soft); margin-top:2px; font-family:'JetBrains Mono', monospace;}
    .copy-btn:hover{background:var(--border);}
    .copy-btn.copied .copy-icon{display:none;}
    .copy-btn .check-icon{display:none;}
    .copy-btn.copied .check-icon{display:block;}

    .dl-panel{background:var(--card); border-radius:var(--radius); box-shadow:var(--shadow); padding:20px 24px;}
    .dl-head{display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;}
    .dl-head h2{font-size:15.5px; font-weight:700; margin:0;}
    .dl-row{display:flex; align-items:center; justify-content:space-between; padding:14px 16px; background:var(--bg); border-radius:10px; margin-bottom:10px; gap:12px; flex-wrap:wrap;}
    .dl-row:last-child{margin-bottom:0;}
    .dl-file-info{display:flex; align-items:center; gap:12px; flex:1; min-width:0;}
    .dl-file-icon{width:38px; height:38px; border-radius:8px; background:var(--green-tint); color:var(--green); display:flex; align-items:center; justify-content:center; flex-shrink:0;}
    .dl-file-name{font-size:13px; font-weight:600; color:var(--ink); word-break:break-all;}
    .dl-file-sub{font-size:11px; color:var(--ink-soft); margin-top:2px; font-family:'Inter',sans-serif;}
    .dl-stats{display:flex; gap:16px; align-items:center; flex-shrink:0;}
    .dl-stat{text-align:center;}
    .dl-stat-label{font-size:10px; text-transform:uppercase; letter-spacing:0.04em; color:var(--ink-soft); margin-bottom:3px;}
    .dl-stat-value{font-size:13px; font-weight:700; color:var(--ink); font-family:'JetBrains Mono', monospace;}
    .dl-stat-value.expired{color:var(--red);}
    .dl-stat-value.ok{color:var(--green);}
    .dl-actions{display:flex; gap:8px; flex-shrink:0;}
    .dl-dl-btn{display:flex; align-items:center; gap:5px; padding:7px 13px; border-radius:7px; border:1.5px solid var(--green); background:var(--green-tint); color:var(--green); font-size:12px; font-weight:600; cursor:pointer; text-decoration:none; font-family:'Inter',sans-serif; transition:background .15s;}
    .dl-dl-btn:hover{background:var(--green); color:#fff;}
    .dl-dl-btn:disabled{opacity:0.4; cursor:not-allowed; pointer-events:none;}
    .dl-edit-btn{display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:7px; border:1.5px solid var(--border); background:#fff; color:var(--ink-soft); cursor:pointer; transition:color .15s, border-color .15s;}
    .dl-edit-btn:hover{color:var(--purple); border-color:var(--purple);}
    .dl-edit-form{display:none; margin-top:12px; padding:14px 16px; background:#fff; border:1px solid var(--border); border-radius:10px; gap:12px; align-items:flex-end;}
    .dl-edit-form.active{display:flex;}
    .dl-edit-field label{display:block; font-size:11px; font-weight:600; color:var(--ink-soft); margin-bottom:4px; text-transform:uppercase; letter-spacing:0.03em;}
    .dl-edit-field input{width:100px; padding:7px 10px; border:1.5px solid var(--border); border-radius:7px; font-size:13px; font-family:'JetBrains Mono', monospace; color:var(--ink); background:#fff;}
    .dl-edit-field input:focus{outline:none; border-color:var(--purple); box-shadow:0 0 0 3px var(--purple-tint);}
    .dl-edit-save{padding:7px 14px; border-radius:7px; background:var(--purple); color:#fff; border:none; font-size:12px; font-weight:600; cursor:pointer; font-family:'Inter',sans-serif;}
    .dl-edit-save:hover{opacity:0.9;}
    .dl-edit-cancel{padding:7px 14px; border-radius:7px; background:var(--bg); color:var(--ink-soft); border:1px solid var(--border); font-size:12px; font-weight:600; cursor:pointer; font-family:'Inter',sans-serif;}

    .fraud-card{background:#FEFAE8; border:1px solid #F4DD8F; border-radius:14px; padding:18px 22px 20px;}
    .fraud-head{display:flex; align-items:center; gap:10px; margin-bottom:16px;}
    .fraud-icon{width:22px; height:22px; border-radius:50%; background:var(--amber); color:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0;}
    .fraud-icon svg{width:13px; height:13px;}
    .fraud-head h3{font-size:14.5px; font-weight:700; margin:0; color:var(--ink);}
    .fraud-grid{display:grid; grid-template-columns:repeat(4, 1fr); gap:22px 28px;}
    .fraud-item .fraud-label{font-size:12.5px; font-weight:700; color:#B4790A; margin-bottom:6px;}
    .fraud-item .fraud-main{font-size:13px; color:var(--ink); font-weight:500;}
    .fraud-item .fraud-sub{font-size:12px; color:var(--purple); margin-top:3px;}
    @media (max-width:900px){.fraud-grid{grid-template-columns:repeat(2, 1fr);}}

    .fraud-check-btn{display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; background:var(--amber); color:#fff; border:none; font-size:12px; font-weight:600; cursor:pointer; font-family:'Inter',sans-serif; transition:opacity .15s;}
    .fraud-check-btn:hover{opacity:0.85;}
    .fraud-check-btn:disabled{opacity:0.5; cursor:not-allowed;}
    .fraud-check-btn svg{flex-shrink:0;}
    .fraud-results{margin-top:16px; padding-top:16px; border-top:1px dashed rgba(245,166,35,0.3);}
    .fraud-results-title{font-size:12px; font-weight:700; color:var(--ink); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:12px;}
    .fraud-courier-grid{display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:10px; margin-bottom:14px;}
    .fraud-courier-card{display:flex; align-items:center; gap:10px; padding:10px 12px; background:#fff; border:1px solid var(--border); border-radius:10px;}
    .fraud-courier-logo{width:28px; height:28px; border-radius:6px; flex-shrink:0; object-fit:contain;}
    .fraud-courier-info{flex:1; min-width:0;}
    .fraud-courier-name{font-size:12px; font-weight:600; color:var(--ink);}
    .fraud-courier-stat{font-size:11px; color:var(--ink-soft);}
    .fraud-courier-ratio{font-size:13px; font-weight:700; font-family:'JetBrains Mono', monospace;}
    .fraud-courier-ratio.good{color:var(--green);}
    .fraud-courier-ratio.warn{color:var(--amber);}
    .fraud-courier-ratio.bad{color:var(--red);}
    .fraud-reports{margin-top:10px;}
    .fraud-report-item{display:flex; align-items:flex-start; gap:10px; padding:10px 12px; background:var(--red-tint); border:1px solid rgba(163,30,34,0.15); border-radius:10px; margin-bottom:8px;}
    .fraud-report-item:last-child{margin-bottom:0;}
    .fraud-report-icon{width:24px; height:24px; border-radius:50%; background:var(--red); color:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:11px; font-weight:700;}
    .fraud-report-text{flex:1;}
    .fraud-report-name{font-size:12.5px; font-weight:600; color:var(--ink);}
    .fraud-report-detail{font-size:11.5px; color:var(--ink-soft); margin-top:1px;}
    .fraud-report-courier{font-size:11px; color:var(--ink-soft); margin-top:2px;}
    .fraud-summary{display:flex; gap:16px; padding:10px 12px; background:#fff; border:1px solid var(--border); border-radius:10px; margin-bottom:14px;}
    .fraud-summary-item{text-align:center;}
    .fraud-summary-label{font-size:10px; text-transform:uppercase; letter-spacing:0.04em; color:var(--ink-soft); margin-bottom:2px;}
    .fraud-summary-value{font-size:14px; font-weight:700; font-family:'JetBrains Mono', monospace;}
    .fraud-error{padding:10px 12px; background:var(--red-tint); border:1px solid rgba(163,30,34,0.15); border-radius:8px; font-size:12.5px; color:var(--red);}

    .geo-block{margin-top:16px; padding-top:16px; border-top:1px dashed rgba(245,166,35,0.3);}
    .geo-title{font-size:12px; font-weight:700; color:var(--ink); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:12px;}
    .geo-status{display:flex; align-items:center; gap:8px; padding:10px 12px; background:var(--red-tint); border:1px solid rgba(163,30,34,0.15); border-radius:8px; font-size:12.5px; color:var(--red);}
    .geo-status.loading{background:var(--amber-tint); border:1px solid rgba(245,166,35,0.35); color:var(--amber);}
    .geo-status.spinner{animation:geo-spin .8s linear infinite;}
    @keyframes geo-spin{to{transform:rotate(360deg);}}
    .geo-info{display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:10px; margin-bottom:12px;}
    .geo-info-item{background:#fff; border:1px solid var(--border); border-radius:10px; padding:8px 12px;}
    .geo-info-label{font-size:10px; text-transform:uppercase; letter-spacing:0.04em; color:var(--ink-soft); margin-bottom:2px;}
    .geo-info-value{font-size:12.5px; font-weight:600; color:var(--ink);}
    .geo-map{height:260px; border-radius:12px; overflow:hidden; border:1px solid var(--border); z-index:0;}
    .geo-map .leaflet-container{height:100%; width:100%;}
    .leaflet-popup-content{margin:10px 14px; font-size:12.5px; line-height:1.5;}

    .act-panel{background:var(--card); border-radius:var(--radius); box-shadow:var(--shadow); padding:20px 24px;}
    .act-head{display:flex; align-items:center; justify-content:space-between; margin-bottom:18px;}
    .act-head h2{font-size:15.5px; font-weight:700; margin:0;}
    .act-timeline{position:relative; padding-left:24px;}
    .act-timeline::before{content:''; position:absolute; left:7px; top:4px; bottom:4px; width:2px; background:var(--border); border-radius:2px;}
    .act-item{position:relative; padding-bottom:18px;}
    .act-item:last-child{padding-bottom:0;}
    .act-dot{position:absolute; left:-24px; top:3px; width:16px; height:16px; border-radius:50%; background:var(--border); z-index:1; display:flex; align-items:center; justify-content:center;}
    .act-dot svg{width:9px; height:9px;}
    .act-item.placed .act-dot{background:var(--red); color:#fff;}
    .act-item.status .act-dot{background:var(--green); color:#fff;}
    .act-item.courier .act-dot{background:var(--blue); color:#fff;}
    .act-item.payment .act-dot{background:var(--amber); color:#fff;}
    .act-item.note .act-dot{background:var(--purple); color:#fff;}
    .act-text{font-size:13px; font-weight:600; color:var(--ink); margin-bottom:2px;}
    .act-text .act-status-tag{display:inline-block; padding:1px 8px; border-radius:999px; font-size:11px; font-weight:700; margin-left:3px;}
    .act-text .act-status-tag.placed{background:var(--red-tint); color:var(--red);}
    .act-text .act-status-tag.confirmed{background:var(--blue-tint); color:var(--blue);}
    .act-text .act-status-tag.processing{background:var(--blue-tint); color:var(--blue);}
    .act-text .act-status-tag.shipped{background:var(--purple-tint); color:var(--purple);}
    .act-text .act-status-tag.delivered{background:var(--green-tint); color:var(--green);}
    .act-text .act-status-tag.completed{background:var(--green-tint); color:var(--green);}
    .act-text .act-status-tag.cancelled{background:var(--red-tint); color:var(--red);}
    .act-text .act-status-tag.returned{background:var(--border); color:var(--ink-soft);}
    .act-time{font-size:11.5px; color:var(--ink-soft); font-family:'JetBrains Mono', monospace;}
    .act-sub{font-size:12px; color:var(--ink-soft); margin-top:2px;}
</style>
@endpush

<x-admin-layout>
    <div class="ao-mono" x-data>

        {{-- Topbar --}}
        <div class="ao-topbar">
            <div class="ao-topbar-left">
                <a href="{{ route('admin.orders.index') }}" class="ao-back-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Back to Orders
                </a>
                <div class="ao-title-row">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <h1>#{{ $order->order_number }}</h1>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $order->order_number }}'); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),1500)" class="copy-btn" style="background:none; border:none; cursor:pointer; padding:4px; border-radius:6px; transition:background .15s;" title="Copy order number">
                            <svg class="copy-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                            <svg class="check-icon" style="display:none; color:var(--green);" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        </button>
                    </div>
                    @php
                    $statusBadgeClass = match($order->status) {
                        'completed', 'delivered' => 'delivered',
                        'pending' => 'pending',
                        'confirmed' => 'processing',
                        'processing' => 'processing',
                        'shipped' => 'shipped',
                        'cancelled' => 'cancelled',
                        'returned' => 'returned',
                        default => 'pending',
                    };
                    $statusLabel = match($order->status) {
                        'completed' => 'Completed',
                        'delivered' => 'Delivered',
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'cancelled' => 'Cancelled',
                        'returned' => 'Returned',
                        default => ucfirst($order->status),
                    };
                    @endphp
                    <span class="ao-status-badge {{ $statusBadgeClass }}">{{ $statusLabel }}</span>
                </div>
                <div class="ao-topbar-meta" style="font-family:'Inter',sans-serif;">Placed on {{ $order->created_at->format('M j, Y \a\t g:i A') }}</div>
            </div>
            <div class="ao-topbar-actions">
                <a href="{{ route('admin.orders.invoice', $order) }}" class="ao-btn" style="text-decoration:none;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
                    Print Invoice
                </a>
                <a href="{{ route('admin.orders.shipping-label', $order) }}" class="ao-btn" style="text-decoration:none;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
                    Shipping Label
                </a>
                <a href="{{ route('admin.orders.edit', $order) }}" class="ao-btn primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.1 2.1 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit Order
                </a>
            </div>
        </div>

        {{-- Status Stepper --}}
        @php
            $steps = [
                ['key' => 'pending', 'label' => 'Placed'],
                ['key' => 'confirmed', 'label' => 'Confirmed'],
                ['key' => 'processing', 'label' => 'Processing'],
                ['key' => 'shipped', 'label' => 'Shipped'],
                ['key' => 'completed', 'label' => 'Delivered'],
            ];
            $statusIndex = array_search($order->status, array_column($steps, 'key'));
            $isTerminal = in_array($order->status, ['cancelled', 'returned']);
            if ($order->status === 'delivered' || $order->status === 'completed') {
                $statusIndex = 4;
            }
            $historyMap = $order->statusHistory->keyBy('status');
            $allDone = in_array($order->status, ['delivered', 'completed']);
        @endphp
        <div class="ao-panel" style="margin-bottom:18px;">
            <div class="ao-stepper">
                @foreach ($steps as $i => $step)
                    @php
                        $done = !$isTerminal && $statusIndex !== false && $allDone ? true : (!$isTerminal && $statusIndex !== false && $i < $statusIndex);
                        $current = !$isTerminal && !$allDone && $statusIndex !== false && $i === $statusIndex;
                        $historyEntry = $historyMap->get($step['key']);
                    @endphp
                    <div class="ao-step {{ $done ? 'done' : '' }} {{ $current ? 'current' : '' }}">
                        @if (!$loop->last)
                            <div class="ao-step-line"></div>
                        @endif
                        <div class="ao-step-dot">
                            @if ($done || $current)
                                &#10003;
                            @else
                                {{ $i + 1 }}
                            @endif
                        </div>
                        <div class="ao-step-label">{{ $step['label'] }}</div>
                        <div class="ao-step-time" style="font-family:'Inter',sans-serif;">
                            @if ($historyEntry)
                                {{ $historyEntry->changed_at->format('M j, g:i A') }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if ($isTerminal)
                <div style="margin-top:12px;">
                    <span class="ao-status-badge {{ $order->status === 'cancelled' ? 'cancelled' : 'returned' }}">
                        Order {{ $order->status }}
                    </span>
                </div>
            @endif
        </div>

        {{-- Content Grid --}}
        <div class="ao-content-grid">

            {{-- Left Column --}}
            <div class="ao-col-left">

                {{-- Fraud Context --}}
                @php
                    $customerOrders = $order->user ? $order->user->orders()->latest()->get() : collect();
                    $phoneOrders = $order->phone ? \App\Models\Order::where('phone', $order->phone)->where('id', '!=', $order->id)->latest()->get() : collect();
                    $ipOrders = $order->ip_address ? \App\Models\Order::where('ip_address', $order->ip_address)->where('id', '!=', $order->id)->latest()->get() : collect();
                    $sameIpDevice = $order->ip_address && $order->device_id
                        ? \App\Models\Order::where('ip_address', $order->ip_address)->where('device_id', $order->device_id)->where('id', '!=', $order->id)->latest()->get()
                        : collect();

                    $formatStats = function ($orders) {
                        $total = $orders->count();
                        $shipped = $orders->whereIn('status', ['shipped', 'delivered', 'completed'])->count();
                        $cancelled = $orders->where('status', 'cancelled')->count();
                        $avg = $total > 0 ? $orders->avg('total_price') : 0;
                        return compact('total', 'shipped', 'cancelled', 'avg');
                    };
                    $accountStats = $formatStats($customerOrders);
                    $phoneStats = $formatStats($phoneOrders);
                    $ipStats = $formatStats($ipOrders);
                    $ipDeviceStats = $formatStats($sameIpDevice);
                @endphp
                <div class="fraud-card" x-data="{
                    checking: false,
                    result: {{ $order->fraudCheck ? json_encode(['success' => true, 'data' => $order->fraudCheck->data, 'reports' => $order->fraudCheck->reports]) : 'null' }},
                    error: null,
                    async runCheck() {
                        this.checking = true;
                        this.error = null;
                        try {
                            const res = await fetch('{{ route('admin.orders.check-fraud', $order) }}');
                            const data = await res.json();
                            if (data.success) {
                                this.result = data;
                            } else {
                                this.error = data.message || 'Check failed.';
                            }
                        } catch (e) {
                            this.error = 'Network error. Please try again.';
                        } finally {
                            this.checking = false;
                        }
                    }
                }">
                    <div class="fraud-head" style="justify-content:space-between;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="fraud-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg>
                            </div>
                            <h3>Customer &amp; order history (fraud context)</h3>
                        </div>
                        <button type="button" class="fraud-check-btn" @click="runCheck" :disabled="checking">
                            <svg x-show="!checking" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                            <svg x-show="checking" x-cloak width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 12a9 9 0 11-6.2-8.56"/></svg>
                            <span x-text="checking ? 'Checking...' : 'Check Fraud'"></span>
                        </button>
                    </div>
                    <div class="fraud-grid">
                        <div class="fraud-item">
                            <div class="fraud-label">IP address</div>
                            <div class="fraud-main mono">{{ $order->ip_address ?: '—' }}</div>
                        </div>
                        <div class="fraud-item">
                            <div class="fraud-label">Same account</div>
                            <div class="fraud-main">{{ $accountStats['total'] }} order{{ $accountStats['total'] !== 1 ? 's' : '' }} · Shipped: {{ $accountStats['shipped'] }}, Cancelled: {{ $accountStats['cancelled'] }}</div>
                            @if ($accountStats['total'] > 0)
                                <div class="fraud-sub mono">Avg order: {{ $currencySymbol }}{{ number_format($accountStats['avg'], 2) }}</div>
                            @endif
                        </div>
                        <div class="fraud-item">
                            <div class="fraud-label">Same phone</div>
                            <div class="fraud-main">{{ $phoneStats['total'] }} order{{ $phoneStats['total'] !== 1 ? 's' : '' }} · Shipped: {{ $phoneStats['shipped'] }}, Cancelled: {{ $phoneStats['cancelled'] }}</div>
                            @if ($phoneStats['total'] > 0)
                                <div class="fraud-sub mono">Avg order: {{ $currencySymbol }}{{ number_format($phoneStats['avg'], 2) }}</div>
                            @endif
                        </div>
                        <div class="fraud-item">
                            <div class="fraud-label">Orders from this IP</div>
                            <div class="fraud-main">{{ $ipStats['total'] }} order{{ $ipStats['total'] !== 1 ? 's' : '' }}</div>
                            @if ($ipStats['total'] > 0)
                                <div class="fraud-sub mono">Avg: {{ $currencySymbol }}{{ number_format($ipStats['avg'], 2) }}</div>
                            @endif
                        </div>
                        <div class="fraud-item">
                            <div class="fraud-label">Same IP + device</div>
                            <div class="fraud-main">{{ $ipDeviceStats['total'] }} order{{ $ipDeviceStats['total'] !== 1 ? 's' : '' }}</div>
                            @if ($ipDeviceStats['total'] > 0)
                                <div class="fraud-sub mono">Avg: {{ $currencySymbol }}{{ number_format($ipDeviceStats['avg'], 2) }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- IP Location (geo) --}}
                    <div class="geo-block" x-data="orderGeo('{{ route('admin.orders.geo', $order) }}')">
                        <div class="geo-title">IP Location</div>

                        <div x-show="state === 'loading'" class="geo-status loading">
                            <svg class="geo-status-spinner" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 12a9 9 0 11-6.2-8.56"/></svg>
                            Locating IP…
                        </div>

                        <div x-show="state === 'error'" x-cloak class="geo-status" x-text="message"></div>

                        <template x-if="state === 'ok'">
                            <div>
                                <div class="geo-info">
                                    <div class="geo-info-item" x-show="data.city">
                                        <div class="geo-info-label">City</div>
                                        <div class="geo-info-value" x-text="data.city"></div>
                                    </div>
                                    <div class="geo-info-item" x-show="data.region">
                                        <div class="geo-info-label">Region</div>
                                        <div class="geo-info-value" x-text="data.region"></div>
                                    </div>
                                    <div class="geo-info-item" x-show="data.country">
                                        <div class="geo-info-label">Country</div>
                                        <div class="geo-info-value" x-text="data.country"></div>
                                    </div>
                                    <div class="geo-info-item" x-show="data.isp">
                                        <div class="geo-info-label">ISP</div>
                                        <div class="geo-info-value" x-text="data.isp"></div>
                                    </div>
                                    <div class="geo-info-item" x-show="data.timezone">
                                        <div class="geo-info-label">Timezone</div>
                                        <div class="geo-info-value" x-text="data.timezone"></div>
                                    </div>
                                </div>
                                <div class="geo-map" x-ref="mapEl"></div>
                            </div>
                        </template>
                    </div>

                    {{-- Fraud API Results --}}
                    <div x-show="result" x-cloak style="margin-top:16px; padding-top:16px; border-top:1px dashed rgba(245,166,35,0.3);">
                        <div class="fraud-results-title">BD Courier — Phone Check Results</div>

                        {{-- Summary --}}
                        <div x-show="result && result.data && result.data.summary">
                            <div class="fraud-summary">
                                <div class="fraud-summary-item">
                                    <div class="fraud-summary-label">Total Parcels</div>
                                    <div class="fraud-summary-value" x-text="result?.data?.summary?.total_parcel"></div>
                                </div>
                                <div class="fraud-summary-item">
                                    <div class="fraud-summary-label">Delivered</div>
                                    <div class="fraud-summary-value" style="color:var(--green);" x-text="result?.data?.summary?.success_parcel"></div>
                                </div>
                                <div class="fraud-summary-item">
                                    <div class="fraud-summary-label">Cancelled</div>
                                    <div class="fraud-summary-value" style="color:var(--red);" x-text="result?.data?.summary?.cancelled_parcel"></div>
                                </div>
                                <div class="fraud-summary-item">
                                    <div class="fraud-summary-label">Success Rate</div>
                                    <div class="fraud-summary-value" :class="(result?.data?.summary?.success_ratio ?? 0) >= 80 ? 'good' : (result?.data?.summary?.success_ratio ?? 0) >= 50 ? 'warn' : 'bad'" x-text="(result?.data?.summary?.success_ratio ?? 0) + '%'"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Per-courier cards --}}
                        <div x-show="result && result.data">
                            <div class="fraud-courier-grid">
                                <template x-for="(info, key) in (result?.data || {})" :key="key">
                                        <div x-show="key !== 'summary' && info && typeof info === 'object' && info.name && (info.total_parcel ?? 0) > 0" class="fraud-courier-card">
                                        <img :src="info?.logo" :alt="info?.name" class="fraud-courier-logo" onerror="this.style.display='none'">
                                        <div class="fraud-courier-info">
                                            <div class="fraud-courier-name" x-text="info?.name"></div>
                                            <div class="fraud-courier-stat" x-text="(info?.total_parcel ?? 0) + ' parcels · ' + (info?.success_parcel ?? 0) + ' Delivered, ' + (info?.cancelled_parcel ?? 0) + ' cancelled'"></div>
                                        </div>
                                        <div class="fraud-courier-ratio" :class="(info?.success_ratio ?? 0) >= 80 ? 'good' : (info?.success_ratio ?? 0) >= 50 ? 'warn' : 'bad'" x-text="(info?.success_ratio ?? 0) + '%'"></div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Fraud Reports --}}
                        <div x-show="result && result.reports && result.reports.length > 0">
                            <div class="fraud-reports">
                                <div class="fraud-results-title" style="color:var(--red);">Fraud Reports</div>
                                <template x-for="report in (result?.reports || [])" :key="report.id">
                                    <div class="fraud-report-item">
                                        <div class="fraud-report-icon">!</div>
                                        <div class="fraud-report-text">
                                            <div class="fraud-report-name" x-text="report.name"></div>
                                            <div class="fraud-report-detail" x-text="report.details"></div>
                                            <div class="fraud-report-courier">
                                                <span x-text="report.courierName"></span> · <span x-text="new Date(report.created_at).toLocaleDateString()"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>


                    </div>

                    {{-- Error --}}
                    <div x-show="error" x-cloak style="margin-top:16px;">
                        <div class="fraud-results">
                            <div class="fraud-error" x-text="error"></div>
                        </div>
                    </div>
                </div>

                {{-- Order Items --}}
                <div class="ao-panel">
                    <div class="ao-panel-head">
                        <h2>Order Items</h2>
                        <span class="ao-count-tag">{{ $order->items->count() }} item{{ $order->items->count() !== 1 ? 's' : '' }}</span>
                    </div>
                    <table class="ao-items">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="num">Qty</th>
                                <th class="num">Unit Price</th>
                                <th class="num">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="ao-item-cell">
                                            <div class="ao-item-thumb">
                                                @if ($item->product && $item->product->image)
                                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}">
                                                @else
                                                    {{ strtoupper(substr($item->product->name ?? 'P', 0, 1)) }}
                                                @endif
                                            </div>
                                            <div>
                                                <div class="ao-item-name" style="font-family:'Inter',sans-serif;">{{ $item->product->name ?? 'Deleted Product' }}</div>
                                                @if ($item->variant)
                                                    <div class="ao-item-variant" style="font-family:'Inter',sans-serif; color:var(--purple); font-weight:600;">
                                                        @foreach(explode(' / ', $item->variant) as $v)
                                                            <span style="display:inline-block; background:var(--purple-tint); color:var(--purple); font-size:11px; padding:2px 7px; border-radius:4px; margin-right:4px; margin-top:2px;">{{ $v }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if ($item->custom_fields_data && count($item->custom_fields_data) > 0)
                                                    @foreach ($item->custom_fields_data as $label => $value)
                                                        <div class="ao-item-variant" style="font-family:'Inter',sans-serif;">
                                                            <span style="color:var(--ink-soft);">{{ $label }}:</span>
                                                            @if (is_array($value))
                                                                <span style="color:var(--ink); font-weight:500;">{{ basename($value['path'] ?? $value['name'] ?? json_encode($value)) }}</span>
                                                            @elseif(is_string($value) && preg_match('/\.\w{2,5}$/', $value) && str_contains($value, '/'))
                                                                <a href="{{ asset('storage/' . $value) }}" target="_blank" style="color:var(--blue); font-weight:500; text-decoration:underline;">{{ basename($value) }}</a>
                                                            @else
                                                                <span style="color:var(--ink); font-weight:500;">{{ $value }}</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @endif
                                                @if ($item->product && $item->product->sku)
                                                    <div class="ao-item-variant" style="font-family:'Inter',sans-serif;">SKU: {{ $item->product->sku }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="num">{{ $item->quantity }}</td>
                                    <td class="num">{{ $currencySymbol }}{{ number_format($item->price, 2) }}</td>
                                    <td class="num">{{ $currencySymbol }}{{ number_format($item->price * $item->quantity, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="ao-totals-block">
                        <div class="ao-total-row">
                            <span>Subtotal</span>
                            <span>{{ $currencySymbol }}{{ number_format($order->items->sum(fn ($i) => $i->price * $i->quantity), 2) }}</span>
                        </div>
                        @if ($order->discount_amount > 0)
                            <div class="ao-total-row discount">
                                <span>Discount{{ $order->promo_code ? ' (' . $order->promo_code . ')' : '' }}</span>
                                <span>&minus; {{ $currencySymbol }}{{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="ao-total-row">
                            <span>Delivery charge</span>
                            <span>{{ $order->delivery_charge > 0 ? $currencySymbol . number_format($order->delivery_charge, 2) : 'Free' }}</span>
                        </div>
                        <div class="ao-total-row grand">
                            <span>Total</span>
                            <span>{{ $currencySymbol }}{{ number_format($order->total_price, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Customer Note --}}
                @if ($order->order_note)
                    <div class="ao-panel">
                        <div class="ao-panel-head" style="margin-bottom:12px;">
                            <h2>Customer Note</h2>
                        </div>
                        <div class="ao-note-box">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg>
                            {{ $order->order_note }}
                        </div>
                    </div>
                @endif

                @if ($order->internal_note)
                    <div class="ao-panel">
                        <div class="ao-panel-head" style="margin-bottom:12px;">
                            <h2>Internal Note</h2>
                        </div>
                        <div class="ao-internal-note-box">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            {{ $order->internal_note }}
                        </div>
                    </div>
                @endif

                {{-- Digital Downloads --}}
                @if ($order->hasDigitalItems() && $order->downloadTokens->isNotEmpty())
                    <div class="dl-panel" x-data="{
                        editing: null,
                        updateForm: { download_limit: '', download_expiry_days: '' },
                        toggleEdit(tokenId, limit, expiry) {
                            if (this.editing === tokenId) {
                                this.editing = null;
                            } else {
                                this.editing = tokenId;
                                this.updateForm.download_limit = limit ?? '';
                                this.updateForm.download_expiry_days = expiry ?? '';
                            }
                        }
                    }">
                        <div class="dl-head">
                            <h2>Digital Downloads</h2>
                            <span class="ao-count-tag">{{ $order->downloadTokens->count() }} file{{ $order->downloadTokens->count() !== 1 ? 's' : '' }}</span>
                        </div>

                        @foreach ($order->downloadTokens as $token)
                            @php
                                $limitReached = $token->isLimitReached();
                                $expired = $token->isExpired();
                                $blocked = $limitReached || $expired;
                            @endphp
                            <div class="dl-row">
                                <div class="dl-file-info">
                                    <div class="dl-file-icon">
                                        @if (str_ends_with(strtolower($token->file_name), '.pdf'))
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                        @elseif (str_ends_with(strtolower($token->file_name), '.zip') || str_ends_with(strtolower($token->file_name), '.rar') || str_ends_with(strtolower($token->file_name), '.7z'))
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        @else
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="dl-file-name">{{ $token->file_name }}</div>
                                        <div class="dl-file-sub">
                                            {{ $token->download_count }} download{{ $token->download_count !== 1 ? 's' : '' }}
                                            @if ($expired)
                                                &middot; <span style="color:var(--red);">Expired {{ $token->expires_at->format('M j, Y') }}</span>
                                            @elseif ($token->expires_at)
                                                &middot; Expires {{ $token->expires_at->format('M j, Y') }}
                                            @else
                                                &middot; No expiry
                                            @endif
                                            @if ($limitReached)
                                                &middot; <span style="color:var(--red);">Limit reached</span>
                                            @elseif ($token->download_limit)
                                                &middot; Limit: {{ $token->download_count }}/{{ $token->download_limit }}
                                            @else
                                                &middot; Unlimited
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="dl-actions">
                                    <a href="{{ route('download.show', $token->token) }}" class="dl-dl-btn" @if($blocked) disabled @endif>
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        Download
                                    </a>
                                    <button type="button" class="dl-edit-btn" @click="toggleEdit({{ $token->id }}, @js($token->download_limit), @js($token->download_expiry_days))" title="Edit limits">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.1 2.1 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Edit Form --}}
                            <div class="dl-edit-form" :class="{ 'active': editing === {{ $token->id }} }">
                                <form action="{{ route('admin.orders.update-download-token', [$order, $token]) }}" method="POST" style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap; width:100%;">
                                    @csrf
                                    @method('PATCH')
                                    <div class="dl-edit-field">
                                        <label>Download Limit</label>
                                        <input type="number" name="download_limit" min="0" :value="updateForm.download_limit" placeholder="Unlimited">
                                    </div>
                                    <div class="dl-edit-field">
                                        <label>Expiry (days)</label>
                                        <input type="number" name="download_expiry_days" min="0" :value="updateForm.download_expiry_days" placeholder="No expiry">
                                    </div>
                                    <button type="submit" class="dl-edit-save">Save</button>
                                    <button type="button" class="dl-edit-cancel" @click="editing = null">Cancel</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Right Column --}}
            <div class="ao-col-right">

                {{-- Update Status --}}
                <div class="ao-panel ao-info-card">
                    <div class="ao-panel-head" style="margin-bottom:12px;">
                        <h2>Update Status</h2>
                    </div>
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="ao-status-select">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="returned" {{ $order->status === 'returned' ? 'selected' : '' }}>Returned</option>
                        </select>
                        <button type="submit" class="ao-save-btn">Save Status</button>
                    </form>

                    {{-- Push to Courier --}}
                    @if (! $order->consignment_id && $order->status === 'shipped')
                        <div style="display:flex; gap:10px; margin-top:10px; margin-left:-12px;">
                            <form action="{{ route('admin.orders.push-steadfast', $order) }}" method="POST" style="flex:1;">
                                @csrf
                                <button type="submit" class="ao-push-btn">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                                    SteadFast
                                </button>
                            </form>
                            <form action="{{ route('admin.orders.push-pathao', $order) }}" method="POST" style="flex:1;">
                                @csrf
                                <button type="submit" class="ao-push-btn pathao">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                                    Pathao
                                </button>
                            </form>
                            <form action="{{ route('admin.orders.push-redx', $order) }}" method="POST" style="flex:1;">
                                @csrf
                                <button type="submit" class="ao-push-btn redx">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                                    RedX
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- Consignment Info --}}
                    @if ($order->consignment_id)
                        <div class="ao-consignment-box">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                            <div style="flex:1;">
                                <div class="ao-cc-label">{{ match($order->courier_name) { 'pathao' => 'Pathao', 'redx' => 'RedX', default => 'Steadfast' } }} Consignment</div>
                                <div class="ao-cc-value">{{ $order->consignment_id }}</div>
                                @if ($order->tracking_code)
                                    <div class="ao-cc-sub">Tracking: {{ $order->tracking_code }}</div>
                                @endif
                                @if ($order->courier_pushed_at)
                                    <div class="ao-cc-sub">Pushed: {{ $order->courier_pushed_at->format('M j, g:i A') }}</div>
                                @endif
                            </div>
                            @if ($order->tracking_link)
                                <a href="{{ $order->tracking_link }}" target="_blank" rel="noopener" class="ao-btn" style="padding:7px 12px; font-size:12px; white-space:nowrap; text-decoration:none;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                    Track
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Customer --}}
                <div class="ao-panel ao-info-card">
                    <div class="ao-customer-head">
                        @php
                            $initials = strtoupper(substr($order->first_name ?? 'U', 0, 1));
                        @endphp
                        <div class="ao-avatar">{{ $initials }}</div>
                        <div>
                            @if ($order->user)
                                <div class="ao-customer-name-row">
                                    <div class="ao-customer-name">{{ $order->first_name }} {{ $order->last_name }}</div>
                                    <a href="{{ route('admin.customers.show', $order->user) }}" class="ao-view-profile">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.06 12.35a2 2 0 000 .7C3.9 18 7.68 21 12 21s8.1-3 9.94-7.95a2 2 0 000-.7C20.1 6 16.32 3 12 3S3.9 6 2.06 12.35z"/></svg>
                                        View Profile
                                    </a>
                                </div>
                                <div class="ao-customer-sub" style="font-family:'Inter',sans-serif;">Customer{{ $order->user->created_at ? ' since ' . $order->user->created_at->format('M Y') : '' }}</div>
                            @else
                                <div class="ao-customer-name">{{ $order->first_name }} {{ $order->last_name }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="ao-divider"></div>
                    <div class="ao-info-row">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.362 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                        <div>
                            <div class="ao-info-label">Phone</div>
                            <div class="ao-info-value">{{ $order->phone ?? $order->user->phone ?? '—' }}</div>
                        </div>
                    </div>
                    @if ($order->email)
                        <div class="ao-info-row">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="M22 6l-10 7L2 6"/></svg>
                            <div>
                                <div class="ao-info-label">Email</div>
                                <div class="ao-info-value">{{ $order->email }}</div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Shipping Address --}}
                <div class="ao-panel ao-info-card">
                    <div class="ao-panel-head" style="margin-bottom:10px;">
                        <h2>Shipping Address</h2>
                    </div>
                    @if ($order->division)
                        <div class="ao-info-row" style="padding-top:0;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 6-9 12-9 12S3 16 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <div>
                                <div class="ao-info-label">Division</div>
                                <div class="ao-info-value">{{ $order->division }}</div>
                            </div>
                        </div>
                    @endif
                    @if ($order->district)
                        <div class="ao-info-row" style="padding-top:0;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 6-9 12-9 12S3 16 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <div>
                                <div class="ao-info-label">District</div>
                                <div class="ao-info-value">{{ $order->district }}</div>
                            </div>
                        </div>
                    @endif
                    @if ($order->thana)
                        <div class="ao-info-row" style="padding-top:0;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 6-9 12-9 12S3 16 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <div>
                                <div class="ao-info-label">Thana/Upazila</div>
                                <div class="ao-info-value">{{ $order->thana }}</div>
                            </div>
                        </div>
                    @endif
                    @if ($order->address)
                        <div class="ao-info-row" style="padding-top:0;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 6-9 12-9 12S3 16 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <div>
                                <div class="ao-info-label">Full Address</div>
                                <div class="ao-info-value">{{ $order->address }}</div>
                            </div>
                        </div>
                    @endif
                    @if ($order->landmark)
                        <div class="ao-info-row" style="padding-top:0;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 6-9 12-9 12S3 16 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <div>
                                <div class="ao-info-label">Landmark</div>
                                <div class="ao-info-value">{{ $order->landmark }}</div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Payment --}}
                <div class="ao-panel ao-info-card">
                    <div class="ao-panel-head" style="margin-bottom:12px;">
                        <h2>Payment</h2>
                    </div>
                    @php
                        $paymentBadgeClass = match($order->payment_method) {
                            'bkash' => 'ao-bkash',
                            'nagad' => 'ao-nagad',
                            'rocket' => 'ao-rocket',
                            'cod' => 'ao-cod-badge',
                            'card' => 'ao-card-badge',
                            default => 'ao-cod-badge',
                        };
                        $paymentLabel = match($order->payment_method) {
                            'cod' => 'COD',
                            'bkash' => 'bKash',
                            'nagad' => 'Nagad',
                            'rocket' => 'Rocket',
                            'card' => 'Card',
                            'bank' => 'Bank',
                            default => ucfirst($order->payment_method),
                        };
                    @endphp
                    <div class="ao-pay-row">
                        <div class="ao-pay-badge {{ $paymentBadgeClass }}">{{ $paymentLabel }}</div>
                        <div>
                            <div class="ao-pay-name">{{ $paymentLabel }} Payment</div>
                            @if ($order->payment_method !== 'cod')
                                <div class="ao-pay-sub">Transaction confirmed</div>
                            @else
                                <div class="ao-pay-sub">Cash on Delivery</div>
                            @endif
                        </div>
                        @if ($order->payment_status === 'paid')
                            <span class="ao-paid-tag">Paid</span>
                        @else
                            <span class="ao-unpaid-tag">Unpaid</span>
                        @endif
                    </div>
                    <div class="ao-info-row" style="padding-top:0;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                        <div>
                            <div class="ao-info-label">{{ $order->payment_status === 'paid' ? 'Paid on' : 'Expected on delivery' }}</div>
                            <div class="ao-info-value" style="font-family:'Inter',sans-serif; font-weight:500;">{{ $order->payment_status === 'paid' ? $order->updated_at->format('M j, Y, g:i A') : 'Pending' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Activity Log --}}
                @php
                    $activities = collect();

                    $activities->push([
                        'type' => 'placed',
                        'label' => 'Order placed',
                        'sub' => 'Customer submitted the order',
                        'time' => $order->created_at,
                    ]);

                    foreach ($order->statusHistory->sortBy('changed_at') as $h) {
                        $statusType = match($h->status) {
                            'confirmed', 'processing', 'shipped', 'delivered', 'completed' => 'status',
                            'cancelled', 'returned' => 'status',
                            default => 'status',
                        };
                        $activities->push([
                            'type' => $statusType,
                            'label' => 'Status changed to',
                            'statusTag' => $h->status,
                            'time' => $h->changed_at,
                        ]);
                    }

                    if ($order->courier_pushed_at) {
                        $courierLabel = match($order->courier_name) {
                            'pathao' => 'Pathao',
                            'redx' => 'RedX',
                            default => 'Steadfast',
                        };
                        $activities->push([
                            'type' => 'courier',
                            'label' => 'Pushed to ' . $courierLabel,
                            'sub' => 'Consignment: ' . $order->consignment_id,
                            'time' => $order->courier_pushed_at,
                        ]);
                    }

                    $activities = $activities->sortByDesc('time')->values();
                @endphp
                <div class="act-panel">
                    <div class="act-head">
                        <h2>Activity Log</h2>
                        <span class="ao-count-tag">{{ $activities->count() }} event{{ $activities->count() !== 1 ? 's' : '' }}</span>
                    </div>
                    @if ($activities->isEmpty())
                        <div style="font-size:13px; color:var(--ink-soft); text-align:center; padding:16px 0;">No activity yet</div>
                    @else
                        <div class="act-timeline">
                            @foreach ($activities as $act)
                                <div class="act-item {{ $act['type'] }}">
                                    <div class="act-dot">
                                        @if ($act['type'] === 'placed')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>
                                        @elseif ($act['type'] === 'courier')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                                        @else
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="act-text">
                                            {{ $act['label'] }}
                                            @if (isset($act['statusTag']))
                                                <span class="act-status-tag {{ $act['statusTag'] }}">{{ ucfirst($act['statusTag']) }}</span>
                                            @endif
                                        </div>
                                        @if (isset($act['sub']))
                                            <div class="act-sub">{{ $act['sub'] }}</div>
                                        @endif
                                        <div class="act-time">{{ $act['time']->format('M j, Y \a\t g:i A') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('orderGeo', (url) => ({
                    state: 'loading',
                    message: '',
                    data: null,
                    map: null,
                    async init() {
                        try {
                            const res = await fetch(url);
                            const json = await res.json();
                            if (json.success) {
                                this.data = json.data;
                                this.state = 'ok';
                                this.$nextTick(() => this.renderMap());
                            } else {
                                this.message = json.message || 'Unable to locate this IP.';
                                this.state = 'error';
                            }
                        } catch (e) {
                            this.message = 'Failed to load location data.';
                            this.state = 'error';
                        }
                    },
                    renderMap() {
                        const el = this.$refs.mapEl;
                        if (!el || !this.data) return;
                        const lat = parseFloat(this.data.latitude);
                        const lng = parseFloat(this.data.longitude);
                        if (isNaN(lat) || isNaN(lng) || typeof L === 'undefined') return;
                        this.map = L.map(el).setView([lat, lng], 12);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                            maxZoom: 19,
                        }).addTo(this.map);
                        L.marker([lat, lng]).addTo(this.map).bindPopup(this.popupText()).openPopup();
                        this.$nextTick(() => this.map?.invalidateSize());
                    },
                    popupText() {
                        const parts = [];
                        if (this.data.city) parts.push(this.data.city);
                        if (this.data.region) parts.push(this.data.region);
                        if (this.data.country) parts.push(this.data.country);
                        const name = parts.join(', ');
                        return name + (this.data.isp ? `<br>${this.data.isp}` : '');
                    },
                }));
            });
        </script>
    @endpush
</x-admin-layout>
