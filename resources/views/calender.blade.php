<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>HRMS Attendance Calendar – Demo</title>
  <style>
    :root{
      --bg:#0b0e14;          /* dark base */
      --panel:#111723;       /* panels */
      --muted:#7a869a;       /* muted text */
      --text:#e6edf3;        /* primary text */
      --brand:#4f86ff;       /* accent */
      --grid:#1b2332;        /* grid lines */
      --ring:rgba(79,134,255,.45);
      --present:#16a34a;     /* green */
      --absent:#ef4444;      /* red */
      --leave:#f59e0b;       /* amber */
      --holiday:#22d3ee;     /* cyan */
      --wfh:#8b5cf6;         /* violet */
      --ot:#06b6d4;          /* teal */
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0; font-family:ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji";
      background:linear-gradient(180deg, #0b0e14 0%, #0d1120 100%);
      color:var(--text);
    }
    .container{max-width:1200px;margin:24px auto;padding:16px}
    header{
      display:grid; gap:12px; align-items:center; margin-bottom:16px;
      grid-template-columns: 1fr; 
    }
    .title{
      display:flex; align-items:center; gap:10px; font-weight:700; font-size:clamp(18px,2.5vw,24px);
    }
    .controls{display:flex; flex-wrap:wrap; gap:8px; align-items:center}
    .controls button, .controls select{
      background:var(--panel); color:var(--text); border:1px solid #1f2937; border-radius:10px; padding:8px 12px; cursor:pointer;
    }
    .controls button:hover, .controls select:hover{border-color:#2b3a55}
    #monthLabel{padding:8px 12px; background:#0e1525; border:1px solid #1f2937; border-radius:10px; font-weight:600}

    .legend{display:flex; gap:12px; flex-wrap:wrap; color:var(--muted); font-size:13px}
    .legend .dot{width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:6px}

    .calendar{
      background:rgba(17,23,35,.65); border:1px solid #1b2332; border-radius:16px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,.35);
    }
    .weekdays{display:grid; grid-template-columns:repeat(7,1fr); background:#0e1525; border-bottom:1px solid #1b2332}
    .weekdays div{padding:10px; text-align:center; color:var(--muted); font-size:12px; letter-spacing:.02em}

    .grid{display:grid; grid-template-columns:repeat(7,1fr); gap:0;}
    .cell{position:relative; min-height:108px; border-right:1px solid var(--grid); border-bottom:1px solid var(--grid); padding:8px; outline:none}
    .cell:nth-child(7n){border-right:none}
    .date{
      font-weight:700; font-size:14px; color:#cbd5e1; display:flex; align-items:center; gap:6px
    }
    .date .today{display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:6px; background:var(--brand); color:#fff}
    .date .num{opacity:.9}

    .badges{margin-top:8px; display:flex; gap:6px; flex-wrap:wrap}
    .badge{font-size:12px; padding:4px 8px; border-radius:999px; border:1px solid rgba(255,255,255,.1); background:rgba(255,255,255,.04)}
    .badge.present{background:rgba(22,163,74,.12); border-color:rgba(22,163,74,.35); color:#bbf7d0}
    .badge.absent{background:rgba(239,68,68,.12); border-color:rgba(239,68,68,.35); color:#fecaca}
    .badge.leave{background:rgba(245,158,11,.12); border-color:rgba(245,158,11,.35); color:#fde68a}
    .badge.holiday{background:rgba(34,211,238,.12); border-color:rgba(34,211,238,.35); color:#a5f3fc}
    .badge.wfh{background:rgba(139,92,246,.12); border-color:rgba(139,92,246,.35); color:#ddd6fe}
    .badge.ot{background:rgba(6,182,212,.12); border-color:rgba(6,182,212,.35); color:#99f6e4}

    .times{margin-top:6px; color:#cbd5e1; font-size:12px; opacity:.9}
    .note{margin-top:4px; color:#93c5fd; font-size:12px}

    .cell.weekend{background:rgba(79,134,255,.04)}
    .cell.holiday{background:linear-gradient(180deg, rgba(34,211,238,.10), rgba(17,23,35,.65))}
    .cell.absent{background:linear-gradient(180deg, rgba(239,68,68,.08), rgba(17,23,35,.65))}

    .cell:focus{box-shadow:0 0 0 2px var(--ring) inset}
    .cell:hover{background:rgba(255,255,255,.02)}

    .sidepanel{
      margin-top:16px; background:rgba(17,23,35,.65); border:1px solid #1b2332; border-radius:14px; padding:14px; display:grid; gap:10px
    }
    .sidepanel h3{margin:0; font-size:16px}
    .sidepanel label{font-size:13px; color:var(--muted)}
    .sidepanel select, .sidepanel input, .sidepanel textarea{
      width:100%; background:#0e1525; color:var(--text); border:1px solid #223149; border-radius:10px; padding:8px
    }
    .sidepanel button{
      justify-self:start; background:var(--brand); color:#fff; border:none; padding:10px 14px; border-radius:10px; cursor:pointer; font-weight:600
    }
    .sidepanel .row{display:grid; gap:8px}

    @media (min-width: 900px){
      header{grid-template-columns: 1fr auto}
      .layout{display:grid; grid-template-columns: 1.2fr .8fr; gap:16px}
    }
  </style>
</head>
<body>
  <div class="container">
    <header>
      <div class="title">📅 HRMS Attendance Calendar</div>
      <div class="controls" aria-label="Calendar controls">
        <button id="prevBtn" title="Previous Month" aria-label="Previous Month">⟨</button>
        <div id="monthLabel" aria-live="polite"></div>
        <button id="nextBtn" title="Next Month" aria-label="Next Month">⟩</button>
        <button id="todayBtn" title="Jump to Today">Today</button>
        <select id="filter" title="Filter by status">
          <option value="all">All</option>
          <option value="present">Present</option>
          <option value="absent">Absent</option>
          <option value="leave">Leave</option>
          <option value="holiday">Holiday</option>
          <option value="wfh">WFH</option>
        </select>
      </div>
      <div class="legend" role="note">
        <span><span class="dot" style="background:var(--present)"></span>Present</span>
        <span><span class="dot" style="background:var(--absent)"></span>Absent</span>
        <span><span class="dot" style="background:var(--leave)"></span>Leave</span>
        <span><span class="dot" style="background:var(--holiday)"></span>Holiday</span>
        <span><span class="dot" style="background:var(--wfh)"></span>WFH</span>
        <span><span class="dot" style="background:var(--ot)"></span>OT</span>
      </div>
    </header>

    <div class="layout">
      <section class="calendar" aria-label="Attendance calendar">
        <div class="weekdays">
          <div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div><div>Sun</div>
        </div>
        <div class="grid" id="grid" role="grid"></div>
      </section>

      <aside class="sidepanel" id="panel" aria-live="polite">
        <h3>Day Details</h3>
        <div class="row"><label>Date</label><input id="panel-date" readonly /></div>
        <div class="row">
          <label>Status</label>
          <select id="panel-status">
            <option value="present">Present</option>
            <option value="absent">Absent</option>
            <option value="leave">Leave</option>
            <option value="holiday">Holiday</option>
            <option value="wfh">WFH</option>
            <option value="ot">OT</option>
          </select>
        </div>
        <div class="row"><label>Check-in</label><input id="panel-in" placeholder="09:45" /></div>
        <div class="row"><label>Check-out</label><input id="panel-out" placeholder="18:15" /></div>
        <div class="row"><label>Note</label><textarea id="panel-note" rows="3" placeholder="Optional note..."></textarea></div>
        <button id="saveBtn">Update Day</button>
        <small style="color:var(--muted)">This is a front-end demo only. Hook these events to your API to persist.</small>
      </aside>
    </div>
  </div>

  <script>
    // ===== Utilities =====
    const pad = n => String(n).padStart(2,'0');
    const fmtKey = (y,m,d) => `${y}-${pad(m+1)}-${pad(d)}`; // m is 0-indexed

    // Week starts Monday; convert JS getDay (0=Sun) => (1..7)
    function isoDay(jsDay){ return jsDay === 0 ? 7 : jsDay; }

    // ===== Demo data (replace with API result) =====
    // Map of YYYY-MM-DD => { status, in, out, note }
    const demoData = new Map();
    (function seed(){
      const now = new Date();
      const sampleMonth = new Date(now.getFullYear(), now.getMonth(), 1);
      const y = sampleMonth.getFullYear();
      const m = sampleMonth.getMonth();
      const days = new Date(y, m+1, 0).getDate();
      const statuses = ['present','present','present','wfh','leave','absent'];
      for(let d=1; d<=days; d++){
        const date = new Date(y, m, d);
        const isWeekend = [6,7].includes(isoDay(date.getDay()));
        let status = isWeekend ? (Math.random()>.2 ? 'holiday' : 'present') : statuses[Math.floor(Math.random()*statuses.length)];
        if (status==='present' || status==='wfh' || status==='ot'){
          demoData.set(fmtKey(y,m,d), {status, in:'09:45', out:'18:10', note: status==='wfh' ? 'Remote' : ''});
        } else if (status==='holiday'){
          demoData.set(fmtKey(y,m,d), {status, note:'Weekend'});
        } else if (status==='leave'){
          demoData.set(fmtKey(y,m,d), {status, note:'Casual Leave'});
        } else if (status==='absent'){
          demoData.set(fmtKey(y,m,d), {status});
        }
      }
    })();

    // ===== Calendar State =====
    const state = {
      view: new Date(), // month in view
      selectedKey: null
    };

    const el = {
      grid: document.getElementById('grid'),
      monthLabel: document.getElementById('monthLabel'),
      prevBtn: document.getElementById('prevBtn'),
      nextBtn: document.getElementById('nextBtn'),
      todayBtn: document.getElementById('todayBtn'),
      filter: document.getElementById('filter'),
      panel: document.getElementById('panel'),
      pDate: document.getElementById('panel-date'),
      pStatus: document.getElementById('panel-status'),
      pIn: document.getElementById('panel-in'),
      pOut: document.getElementById('panel-out'),
      pNote: document.getElementById('panel-note'),
      saveBtn: document.getElementById('saveBtn')
    };

    function setMonthLabel(dt){
      const fmt = new Intl.DateTimeFormat(undefined, { month:'long', year:'numeric' });
      el.monthLabel.textContent = fmt.format(dt);
    }

    function render(){
      const y = state.view.getFullYear();
      const m = state.view.getMonth();
      setMonthLabel(state.view);
      el.grid.innerHTML = '';

      const first = new Date(y, m, 1);
      const daysInMonth = new Date(y, m+1, 0).getDate();
      const lead = isoDay(first.getDay()) - 1; // number of empty cells before day 1

      // Leading blanks
      for(let i=0;i<lead;i++){
        const blank = document.createElement('div');
        blank.className = 'cell';
        blank.setAttribute('aria-disabled','true');
        el.grid.appendChild(blank);
      }

      const filterVal = el.filter.value;
      const todayKey = fmtKey(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());

      for(let d=1; d<=daysInMonth; d++){
        const date = new Date(y, m, d);
        const key = fmtKey(y,m,d);
        const data = demoData.get(key);

        const cell = document.createElement('button');
        cell.className = 'cell';
        cell.type = 'button';
        cell.setAttribute('role','gridcell');
        cell.setAttribute('aria-label', `${date.toDateString()} ${data?data.status:''}`);

        const isWeekend = [6,7].includes(isoDay(date.getDay()));
        if(isWeekend) cell.classList.add('weekend');
        if(data?.status==='holiday') cell.classList.add('holiday');
        if(data?.status==='absent') cell.classList.add('absent');

        const dateEl = document.createElement('div');
        dateEl.className = 'date';
        const isToday = key===todayKey;
        dateEl.innerHTML = isToday
          ? `<span class="today" title="Today">${d}</span>`
          : `<span class="num">${d}</span>`;
        cell.appendChild(dateEl);

        const badges = document.createElement('div');
        badges.className = 'badges';
        if(data?.status){
          const b = document.createElement('span');
          b.className = `badge ${data.status}`;
          b.textContent = data.status.toUpperCase();
          badges.appendChild(b);
        }
        if(data?.in && data?.out){
          const t = document.createElement('span');
          t.className = 'badge';
          t.textContent = `${data.in} – ${data.out}`;
          badges.appendChild(t);
        }
        cell.appendChild(badges);

        if(data?.note){
          const note = document.createElement('div');
          note.className = 'note';
          note.textContent = data.note;
          cell.appendChild(note);
        }

        // Filter logic
        if(filterVal !== 'all' && data?.status !== filterVal){
          cell.style.display = 'none';
        }

        // Click handler to load side panel
        cell.addEventListener('click', () => selectDay(key));
        el.grid.appendChild(cell);
      }
    }

    function selectDay(key){
      state.selectedKey = key;
      const data = demoData.get(key) || {status:'present'};
      const [y,m,d] = key.split('-');
      el.pDate.value = `${d}-${m}-${y}`;
      el.pStatus.value = data.status || 'present';
      el.pIn.value = data.in || '';
      el.pOut.value = data.out || '';
      el.pNote.value = data.note || '';
    }

    // ===== Events =====
    el.prevBtn.addEventListener('click', () => { state.view.setMonth(state.view.getMonth()-1); render(); });
    el.nextBtn.addEventListener('click', () => { state.view.setMonth(state.view.getMonth()+1); render(); });
    el.todayBtn.addEventListener('click', () => { state.view = new Date(); render(); });
    el.filter.addEventListener('change', render);

    el.saveBtn.addEventListener('click', () => {
      if(!state.selectedKey){ alert('Select a day cell first'); return; }
      const payload = {
        status: el.pStatus.value,
        in: el.pIn.value.trim() || undefined,
        out: el.pOut.value.trim() || undefined,
        note: el.pNote.value.trim() || undefined,
      };
      demoData.set(state.selectedKey, payload);
      render();
      // In real app: send to API
      // fetch('/api/attendance', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({date: state.selectedKey, ...payload})})
    });

    // Initial render
    render();
  </script>
</body>
</html>