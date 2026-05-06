<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Thank You</title>
  <meta name="description" content="Thank you for submitting the form. We will be in touch soon.">
  <style>
    :root{
      --bg1: #f6fff5; /* softer green-tinted background */
      --bg2: #f0fff8;
      --accent: #16a34a; /* green-600 */
      --accent-2: #059669; /* green-700 */
      --card-bg: #ffffff;
      --muted: #4b5563;
      --radius: 14px;
      --max-width: 820px;
      --shadow: 0 8px 30px rgba(6,38,13,0.06);
    }

    *{box-sizing: border-box}
    html,body{height:100%;margin:0;font-family:Inter,system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial;line-height:1.4;color:#0f1724; background: linear-gradient(120deg, #63c377 0%, #397c47 100%);}

    .wrap{min-height:100%;display:flex;align-items:center;justify-content:center;padding:48px 20px}

    .card{background:var(--card-bg);max-width:var(--max-width);width:100%;border-radius:var(--radius);box-shadow:var(--shadow);padding:48px;display:grid;grid-template-columns:140px 1fr;gap:28px;align-items:center}

    /* check icon */
    .badge{width:116px;height:116px;border-radius:32px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--accent),var(--accent-2));box-shadow:0 8px 28px rgba(2,6,23,0.12);}
    .badge svg{width:60px;height:60px;display:block;filter:drop-shadow(0 4px 18px rgba(22,163,74,0.28));} 

    h1{margin:0;font-size:clamp(20px,3.4vw,28px);color:#071035}
    p.lead{margin:8px 0 18px;font-size:clamp(14px,2.2vw,16px);color:var(--muted)}

    .actions{display:flex;gap:12px;flex-wrap:wrap}
    .btn{background:var(--accent);color:#fff;padding:10px 16px;border-radius:10px;border:0;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:10px;box-shadow:0 6px 18px rgba(36,99,255,0.18)}
    .btn.secondary{background:transparent;color:var(--accent);border:1px solid rgba(36,99,255,0.12);box-shadow:none}

    .meta{font-size:13px;color:var(--muted)}

    /* responsive */
    @media (max-width:720px){
      .card{grid-template-columns:1fr;row-gap:18px;padding:28px;border-radius:12px}
      .badge{margin:0 auto}
      .actions{justify-content:center}
      .meta{text-align:center}
    }

    /* subtle entrance */
    .card{opacity:0;transform:translateY(8px);animation:enter 420ms cubic-bezier(.2,.9,.3,1) forwards}
    @keyframes enter{to{opacity:1;transform:none}}

    /* simple check animation */
    .check path{stroke-dasharray:160;stroke-dashoffset:160;stroke-width:2.8;stroke:#fff;fill:none;animation:dash 520ms ease-out 140ms forwards}
    @keyframes dash{to{stroke-dashoffset:0}}

    /* small accessibility focus styles */
    a:focus{outline:3px solid rgba(5,150,105,0.12);outline-offset:3px;border-radius:8px}
    /* remove visible outline for the programmatically focused heading to avoid border on load */
    #thank-heading:focus{outline:none;box-shadow:none}

  </style>
</head>
<body>
  <div class="wrap">
    <main class="card" role="main" aria-labelledby="thank-heading">
      <div>
        <div class="badge" aria-hidden="true">
          <!-- check icon -->
          <svg class="check" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img">
            <circle cx="12" cy="12" r="11" stroke="rgba(255,255,255,0.12)" stroke-width="1" fill="rgba(255,255,255,0.06)" />
            <path d="M6.5 12.5l3 3 7-8" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </div>
      </div>

      <div>
        <h1 id="thank-heading">Thank You ! <br> Your form has been Submitted.</h1>
        <p class="lead">We appreciate you taking the time to provide this information. We will be in touch with you soon to follow up.</p>

      

        <div style="height:12px"></div>
        <div class="meta">If you need immediate assistance, contact <a href="mailto:hr@sysllc.com">hr@sysllc.com</a></div>
      </div>
    </main>
  </div>

  <!-- Small inline script to set focus for accessibility -->
 
</body>
</html>