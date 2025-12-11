<?php require 'config.php'; ?>
<!doctype html>
<html lang="en">
<head>
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-F57J36XCNL"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-F57J36XCNL');
</script>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>UPC Crime & Fire Log</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<style>
  :root{
    --bg:#050508;
    --fg:#f5f5f7;
    --muted:#a0a0aa;
    --bd:#2a2a30;
    --card:#101015;
  }

  *{ box-sizing:border-box; }

  html,body{
    margin:0;
    padding:0;
    height:100dvh;
    width:100%;
    font:14px/1.4 system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    background:var(--bg);
    color:var(--fg);
    overflow:hidden;
  }

  /* Fullscreen map behind everything */
  #map{
    position:fixed;
    inset:0;
    z-index:0;
  }

  /* Top navigation bar (mirror home.php style here) */
  .top-nav{
    position:fixed;
    top:0;
    left:0;
    right:0;
    height:52px;
    display:flex;
    align-items:center;
    z-index:10;
    backdrop-filter:blur(18px);
    background:linear-gradient(to bottom, rgba(5,5,8,0.92), rgba(5,5,8,0.65));
    border-bottom:1px solid rgba(255,255,255,0.06);
    padding:0 16px;
  }
  .top-nav-inner{
    display:flex;
    align-items:center;
    justify-content:space-between;
    width:100%;
    max-width:1100px;
    margin:0 auto;
  }
  .brand{
    display:flex;
    align-items:center;
    gap:8px;
    font-weight:600;
    letter-spacing:.03em;
    font-size:14px;
  }
  .brand-dot{
    width:10px;
    height:10px;
    border-radius:999px;
    background:radial-gradient(circle at 30% 20%, #4bffba, #18a4ff);
  }
  .nav-links a{
    margin-left:18px;
    font-size:13px;
    color:var(--muted);
    text-decoration:none;
    padding:4px 0;
    position:relative;
  }
  .nav-links a.active{
    color:var(--fg);
  }
  .nav-links a.active::after{
    content:"";
    position:absolute;
    left:0;
    right:0;
    bottom:-4px;
    height:2px;
    border-radius:999px;
    background:#18ff9c;
  }

  /* Floating control card */
  .controls-panel{
    position:fixed;
    top:68px;        /* just under nav */
    left:16px;
    width:320px;
    max-height:calc(100dvh - 84px);
    padding:14px 14px 12px;
    background:rgba(8,8,12,0.94);
    border-radius:16px;
    border:1px solid rgba(255,255,255,0.06);
    box-shadow:0 18px 40px rgba(0,0,0,0.65);
    z-index:9;
    overflow-y:auto;
  }

  .controls-panel h1{
    font-size:14px;
    margin:0 0 8px;
    letter-spacing:.04em;
    text-transform:uppercase;
    color:var(--muted);
  }

  .controls-panel label{
    display:block;
    font-size:11px;
    color:var(--muted);
    margin:10px 0 3px;
  }

  .controls-panel input,
  .controls-panel select,
  .controls-panel button{
    width:100%;
    padding:8px 10px;
    border-radius:10px;
    border:1px solid var(--bd);
    background:#0c0c12;
    color:var(--fg);
    font-size:12px;
    outline:none;
  }

  .controls-panel input::placeholder{
    color:#5f5f66;
  }

  .controls-panel select{
    appearance:none;
    background-image:linear-gradient(45deg,transparent 50%, #5f5f66 50%),
                     linear-gradient(135deg,#5f5f66 50%, transparent 50%);
    background-position:calc(100% - 18px) 10px, calc(100% - 13px) 10px;
    background-size:5px 5px;
    background-repeat:no-repeat;
  }

  .row{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:6px;
  }

  .btns{
    display:flex;
    gap:6px;
    margin-top:8px;
  }

  .btns button{
    cursor:pointer;
    border-radius:999px;
    border:1px solid var(--bd);
    background:#15151d;
    font-size:11px;
    white-space:nowrap;
  }

  .btns button.primary{
    background:#18ff9c;
    color:#020204;
    border-color:#18ff9c;
    font-weight:500;
  }

  .section-title{
    margin-top:12px;
    font-size:11px;
    font-weight:600;
    letter-spacing:.05em;
    text-transform:uppercase;
    color:var(--muted);
  }

  #routeStatus{
    margin-top:6px;
    font-size:10px;
    color:var(--muted);
  }

  .leaflet-control-attribution{
    filter:grayscale(1) brightness(.8);
  }

  /* Small screens */
  @media (max-width:720px){
    .controls-panel{
      left:50%;
      transform:translateX(-50%);
      width:calc(100% - 24px);
      top:64px;
      max-height:60dvh;
    }
    .nav-links{ display:none; }
  }
</style>
</head>
<body>

<!-- Top nav: mirror home.php here -->
<header class="top-nav">
  <div class="top-nav-inner">
    <div class="brand">
      <span class="brand-dot"></span>
      <a href="home.php" style="text-decoration:none;color:inherit;">SafeRoute Campus</a>
    </div>
    <nav class="nav-links">
      <!-- Adjust these to match exactly what you use in home.php -->
      <a href="home.php">Home</a>
      <a href="map2.php" class="active">Crime Map</a>
      <a href="about.php">About</a>
      <a href="resources.php">Resources</a>
      <a href="stats.php">Analytics</a>
      <?php if (isLoggedIn()): ?>
        <?php if (isAdmin()): ?>
          <a href="admin.php">Admin</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Log in</a>
        <a href="signup.php">Sign up</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<!-- Map background -->
<div id="map"></div>

<!-- Floating search / filters / routing -->
<div class="controls-panel">
  <h1>UPC Crime &amp; Fire Log</h1>

  <label>Search</label>
  <input id="q" placeholder="Offense / location / disposition…">

  <div class="row">
    <div>
      <label>Date From</label>
      <input id="from" placeholder="MM/DD/YYYY">
    </div>
    <div>
      <label>Date To</label>
      <input id="to" placeholder="MM/DD/YYYY">
    </div>
  </div>

  <label>Offense</label>
  <select id="offense">
    <option value="">All offenses</option>
  </select>

  <label>Disposition</label>
  <select id="disposition">
    <option value="">All dispositions</option>
  </select>

  <div class="btns">
    <button id="apply" class="primary">Apply</button>
    <button id="clear">Clear</button>
  </div>
  <div class="btns">
    <button id="prev">Prev</button>
    <button id="next">Next</button>
  </div>

  <div class="section-title">Safe Route</div>
  <label>Travel Mode</label>
  <select id="routeMode">
    <option value="driving">Driving</option>
    <option value="walking">Walking</option>
  </select>
  <div class="btns">
    <button id="setStart">Choose start</button>
    <button id="setEnd">Choose destination</button>
  </div>
  <div class="btns">
    <button id="routeQuick">Fastest</button>
    <button id="routeSafe" class="primary">Reroute to avoid crime</button>
  </div>
  <p id="routeStatus"></p>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
// -------------------- BASIC CONFIG --------------------
var api = 'https://rafaelv8.webdev.iyaserver.com/acad276/testM2.php';
var offset = 0;
var limit  = 100;

// -------------------- MAP --------------------
var map = L.map('map').setView([34.0206, -118.2854], 14);
L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
  maxZoom: 20, attribution: '&copy; OpenStreetMap & CARTO'
}).addTo(map);

var markerLayer = L.layerGroup().addTo(map);

// routing state
var routesLayer = L.layerGroup().addTo(map);
var startMarker = null;
var endMarker   = null;
var routeClickMode = null; // "start" or "end" (for clicking on map)
var travelMode = 'driving'; // "driving" or "walking"
var crimes      = [];   // each crime row gets ._pt latLng

function setRouteStatus(msg){
  document.getElementById('routeStatus').textContent = msg || '';
}

// -------------------- GEO CACHE --------------------
var geoCache = {};
function geocode(address, callback){
  if (!address) { callback(null); return; }
  if (geoCache[address]) { callback(geoCache[address]); return; }

  var xhr = new XMLHttpRequest();
  var url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(address);
  xhr.open('GET', url, true);
  xhr.onreadystatechange = function(){
    if (xhr.readyState === 4) {
      var pt = null;
      try {
        var arr = JSON.parse(xhr.responseText);
        if (arr && arr[0]) {
          pt = [parseFloat(arr[0].lat), parseFloat(arr[0].lon)];
        }
      } catch(e) {}
      geoCache[address] = pt;
      callback(pt);
    }
  };
  xhr.send();
}

// -------------------- QUERY STRING --------------------
function buildQuery(){
  var qs = '';
  var v;

  v = document.getElementById('q').value.trim();
  if (v) qs += '&q=' + encodeURIComponent(v);

  v = document.getElementById('from').value.trim();
  if (v) qs += '&from=' + encodeURIComponent(v);

  v = document.getElementById('to').value.trim();
  if (v) qs += '&to=' + encodeURIComponent(v);

  v = document.getElementById('offense').value;
  if (v) qs += '&offense=' + encodeURIComponent(v);

  v = document.getElementById('disposition').value;
  if (v) qs += '&disposition=' + encodeURIComponent(v);

  qs += '&limit=' + limit + '&offset=' + offset;
  if (qs.charAt(0) === '&') qs = qs.substring(1);
  return qs;
}

// -------------------- FILL SELECTS ONCE --------------------
var selectsFilled = false;
function fillSelectsIfNeeded(rows){
  if (selectsFilled) return;

  var offenseSel = document.getElementById('offense');
  var dispSel    = document.getElementById('disposition');

  var offenses = {}, disps = {};
  for (var i = 0; i < rows.length; i++){
    var o = rows[i].offense;
    var d = rows[i].disposition;
    if (o) offenses[o] = true;
    if (d) disps[d] = true;
  }

  for (var key in offenses){
    var opt1 = document.createElement('option');
    opt1.value = key;
    opt1.textContent = key;
    offenseSel.appendChild(opt1);
  }
  for (var key2 in disps){
    var opt2 = document.createElement('option');
    opt2.value = key2;
    opt2.textContent = key2;
    dispSel.appendChild(opt2);
  }
  selectsFilled = true;
}

// -------------------- LOAD INCIDENTS --------------------
function load(){
  markerLayer.clearLayers();
  routesLayer.clearLayers();
  crimes = [];
  setRouteStatus('');

  var xhr = new XMLHttpRequest();
  xhr.open('GET', api + '?' + buildQuery(), true);
  xhr.onreadystatechange = function(){
    if (xhr.readyState === 4){
      var rows = [];
      try {
        var j = JSON.parse(xhr.responseText);
        if (j && j.rows) rows = j.rows;
      } catch(e){}

      fillSelectsIfNeeded(rows);
      crimes = rows;

      var bounds = [];
      var i = 0;

      function step(){
        if (i >= rows.length){
          if (bounds.length){
            map.fitBounds(bounds, { padding: [40,40] });
          }
          return;
        }
        var r = rows[i++];
        var addr = r.address_for_map || r.location || '';
        geocode(addr, function(pt){
          if (pt){
            var ll = L.latLng(pt[0], pt[1]);
            bounds.push(ll);

            r._pt = ll;

            var html = '<b>' + (r.offense || 'Incident') + '</b><br>' +
                       (r.location || '') + '<br>' +
                       'Reported: ' + (r.date_reported || '') + '<br>' +
                       'Disposition: ' + (r.disposition || '—');

            L.circleMarker(ll, {
              radius:4, weight:1, color:'#000', fillOpacity:0.85
            })
              .bindPopup(html)
              .addTo(markerLayer);
          }
          step();
        });
      }
      step();
    }
  };
  xhr.send();
}

// -------------------- FILTER BUTTONS --------------------
document.getElementById('apply').onclick = function(){
  // Track search
  trackAction('search', {
    search_term: document.getElementById('q').value.trim() || null,
    filter_offense: document.getElementById('offense').value || null,
    filter_disposition: document.getElementById('disposition').value || null,
    date_from: document.getElementById('from').value.trim() || null,
    date_to: document.getElementById('to').value.trim() || null
  });
  offset = 0; load();
};
document.getElementById('clear').onclick = function(){
  document.getElementById('q').value = '';
  document.getElementById('from').value = '';
  document.getElementById('to').value = '';
  document.getElementById('offense').value = '';
  document.getElementById('disposition').value = '';
  offset = 0; load();
};
document.getElementById('prev').onclick = function(){
  offset = Math.max(0, offset - limit); load();
};
document.getElementById('next').onclick = function(){
  offset += limit; load();
};

// -------------------- ROUTING: CLICK START / END --------------------
map.on('click', function(e){
  if (!routeClickMode) return;

  if (routeClickMode === 'start'){
    if (startMarker) map.removeLayer(startMarker);
    startMarker = L.marker(e.latlng, {opacity:0.95})
      .bindPopup('Start')
      .addTo(map)
      .openPopup();
    setRouteStatus('Start set. Now choose destination.');
  } else if (routeClickMode === 'end'){
    if (endMarker) map.removeLayer(endMarker);
    endMarker = L.marker(e.latlng, {opacity:0.95})
      .bindPopup('Destination')
      .addTo(map)
      .openPopup();
    setRouteStatus('Destination set. Pick a routing option.');
  }
  routeClickMode = null;
});

function ensureStartEnd(){
  if (!startMarker || !endMarker){
    setRouteStatus('Pick both a start and a destination on the map.');
    return false;
  }
  return true;
}

// -------------------- DISTANCE HELPERS FOR CRIME SCORING --------------------
function dist(a, b){
  var lat1 = a.lat, lng1 = a.lng;
  var lat2 = b.lat, lng2 = b.lng;
  var x = (lng2 - lng1) * Math.cos((lat1 + lat2) * Math.PI / 360);
  var y = (lat2 - lat1);
  return Math.sqrt(x*x + y*y);
}

function pointToSegmentDistance(p, a, b){
  var ax = a.lng, ay = a.lat;
  var bx = b.lng, by = b.lat;
  var px = p.lng, py = p.lat;

  var vx = bx - ax;
  var vy = by - ay;
  var wx = px - ax;
  var wy = py - ay;

  var c1 = vx*wx + vy*wy;
  if (c1 <= 0) return dist(p, a);

  var c2 = vx*vx + vy*vy;
  if (c2 <= c1) return dist(p, b);

  var t = c1 / c2;
  var proj = L.latLng(ay + t*vy, ax + t*vx);
  return dist(p, proj);
}

function crimeScore(routeLatLngs){
  var score = 0;
  var threshold = 0.0010;

  for (var i = 0; i < crimes.length; i++){
    var r = crimes[i];
    if (!r._pt) continue;
    var p = r._pt;

    for (var j = 0; j < routeLatLngs.length - 1; j++){
      var a = routeLatLngs[j];
      var b = routeLatLngs[j+1];
      var d = pointToSegmentDistance(p, a, b);
      if (d < threshold){
        score++;
        break;
      }
    }
  }
  return score;
}

// -------------------- REAL STREET ROUTES VIA OSRM --------------------
function fetchRoutesFromOSRM(a, b, callback){
  var profile = travelMode; // 'driving' or 'walking'
  var url =
    'https://router.project-osrm.org/route/v1/' + profile + '/' +
    a.lng + ',' + a.lat + ';' + b.lng + ',' + b.lat +
    '?alternatives=true&overview=full&geometries=geojson';

  var xhr = new XMLHttpRequest();
  xhr.open('GET', url, true);
  xhr.onreadystatechange = function(){
    if (xhr.readyState === 4){
      var routes = [];
      try {
        var data = JSON.parse(xhr.responseText);
        if (data && data.routes){
          for (var i = 0; i < data.routes.length; i++){
            var r = data.routes[i];
            var coords = r.geometry.coordinates;
            var latlngs = [];
            for (var k = 0; k < coords.length; k++){
              latlngs.push(L.latLng(coords[k][1], coords[k][0]));
            }
            routes.push({
              latlngs: latlngs,
              distance: r.distance,
              duration: r.duration
            });
          }
        }
      } catch(e){}
      callback(routes);
    }
  };
  xhr.send();
}

function drawRoute(latlngs, options){
  return L.polyline(latlngs, options || {
    weight:4,
    opacity:0.9
  }).addTo(routesLayer);
}

// -------------------- TRACK USER ACTIONS --------------------
function trackAction(action, data){
  var formData = new FormData();
  formData.append('action', action);
  for (var key in data) {
    if (data[key] !== null && data[key] !== undefined) {
      formData.append(key, data[key]);
    }
  }
  fetch('track_action.php', {
    method: 'POST',
    body: formData
  }).catch(function(err) {
    // Silently fail - tracking shouldn't break the app
    console.log('Tracking failed:', err);
  });
}

// -------------------- FASTEST AND SAFE ROUTES --------------------
function routeQuick(){
  if (!ensureStartEnd()) return;
  routesLayer.clearLayers();
  var modeText = travelMode === 'walking' ? 'walking' : 'driving';
  setRouteStatus('Calculating fastest ' + modeText + ' route…');

  var a = startMarker.getLatLng();
  var b = endMarker.getLatLng();

  fetchRoutesFromOSRM(a, b, function(routes){
    if (!routes.length){
      setRouteStatus('No route found from OSRM.');
      return;
    }
    var fastest = routes[0];
    var line = drawRoute(fastest.latlngs, {weight:5, opacity:0.95});
    map.fitBounds(line.getBounds(), {padding:[40,40]});
    var distance = travelMode === 'walking' 
      ? (fastest.distance/1000).toFixed(2) + ' km'
      : (fastest.distance/1000).toFixed(2) + ' km';
    var duration = travelMode === 'walking'
      ? ' (~' + Math.round(fastest.duration / 60) + ' min walk)'
      : ' (~' + Math.round(fastest.duration / 60) + ' min drive)';
    setRouteStatus('Fastest ' + modeText + ' route. Distance ' + distance + duration + '.');
    
    // Track route request
    trackAction('route_request', {
      travel_mode: travelMode,
      route_type: 'fastest',
      start_lat: a.lat,
      start_lng: a.lng,
      end_lat: b.lat,
      end_lng: b.lng
    });
  });
}

function routeSafe(){
  if (!ensureStartEnd()) return;
  routesLayer.clearLayers();
  var modeText = travelMode === 'walking' ? 'walking' : 'driving';
  setRouteStatus('Calculating safer ' + modeText + ' alternatives…');

  var a = startMarker.getLatLng();
  var b = endMarker.getLatLng();

  fetchRoutesFromOSRM(a, b, function(routes){
    if (!routes.length){
      setRouteStatus('No route found from OSRM.');
      return;
    }

    var bestIndex = 0;
    var bestScore = Infinity;
    var bestLength = Infinity;

    for (var i = 0; i < routes.length; i++){
      var r = routes[i];
      var s = crimeScore(r.latlngs);

      var len = 0;
      for (var k = 0; k < r.latlngs.length - 1; k++){
        len += dist(r.latlngs[k], r.latlngs[k+1]);
      }

      if (s < bestScore || (s === bestScore && len < bestLength)){
        bestScore = s;
        bestLength = len;
        bestIndex = i;
      }
    }

    for (var j = 0; j < routes.length; j++){
      var opts;
      if (j === bestIndex){
        opts = {weight:5, opacity:0.95, color:'#18ff9c'};
      } else {
        opts = {weight:3, opacity:0.35, dashArray:'4 6'};
      }
      drawRoute(routes[j].latlngs, opts);
    }

    var bestRoute = routes[bestIndex];
    map.fitBounds(L.polyline(bestRoute.latlngs).getBounds(), {padding:[40,40]});

    var duration = travelMode === 'walking'
      ? ' (~' + Math.round(bestRoute.duration / 60) + ' min walk)'
      : ' (~' + Math.round(bestRoute.duration / 60) + ' min drive)';
    setRouteStatus(
      'Safest of ' + routes.length + ' ' + modeText + ' routes. Approx. nearby incidents: ' + bestScore +
      '. Distance ~' + (bestRoute.distance/1000).toFixed(2) + ' km' + duration + '.'
    );
    
    // Track safe route request
    trackAction('route_request', {
      travel_mode: travelMode,
      route_type: 'safe',
      start_lat: a.lat,
      start_lng: a.lng,
      end_lat: b.lat,
      end_lng: b.lng
    });
  });
}

// -------------------- ROUTING BUTTONS --------------------
document.getElementById('setStart').onclick = function(){
  routeClickMode = 'start';
  setRouteStatus('Click the map to set your start.');
};
document.getElementById('setEnd').onclick = function(){
  routeClickMode = 'end';
  setRouteStatus('Click the map to set your destination.');
};
document.getElementById('routeQuick').onclick = routeQuick;
document.getElementById('routeSafe').onclick  = routeSafe;

// Update travel mode when selector changes
document.getElementById('routeMode').onchange = function(){
  travelMode = this.value;
  setRouteStatus('Travel mode set to ' + travelMode + '. Recalculate route if needed.');
};

// -------------------- FIRST RUN --------------------
load();
</script>
</body>
</html>
