<!--<h1>Nur Muhammad Asnadi - 11210930000019</h1>-->

<div class="content">
    <div id="map" style="width: 100%; height: 530px; color:black;"></div>
</div>
<script>
    var warmad = new L.LayerGroup();
    var kelurahan = new L.LayerGroup();
    var jalan = new L.LayerGroup();

    var map = L.map('map', {
        center: [-6.307001296067748, 106.75738285652359],
        zoom: 15,
        zoomControl: false,
        layers: []
    });

    var GoogleSatelliteHybrid = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        maxZoom: 22,
        attribution: 'Mapping Warmad'
    }).addTo(map);

    var Esri_NatGeoWorldMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/NatGeo_World_Map/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; National Geographic, Esri, DeLorme, NAVTEQ, UNEP-WCMC, USGS, NASA, ESA, METI, NRCAN, GEBCO, NOAA, iPC',
        maxZoom: 16
    });

    var GoogleMaps = new L.TileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        opacity: 1.0,
        attribution: 'Mapping Warmad'
    });
    var GoogleRoads = new L.TileLayer('https://mt1.google.com/vt/lyrs=h&x={x}&y={y}&z={z}', {
        opacity: 1.0,
        attribution: 'Mapping Warmad'
    });

    var baseLayers = {
        'Google Satellite Hybrid': GoogleSatelliteHybrid,
        'Esri_NatGeoWorldMap': Esri_NatGeoWorldMap,
        'GoogleMaps': GoogleMaps,
        'GoogleRoads': GoogleRoads
    };

    var groupedOverlays = {
        "Peta Dasar": {
            'Kelurahan': kelurahan,
            'Jalan': jalan
        },
        "Peta Khusus": {
            'Warung Madura': warmad
        }
    };

    L.control.groupedLayers(baseLayers, groupedOverlays).addTo(map);

    var osmUrl = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
    var osmAttrib = 'Map data &copy; OpenStreetMap contributors';
    var osm2 = new L.TileLayer(osmUrl, {
        minZoom: 0,
        maxZoom: 13,
        attribution: osmAttrib
    });
    var rect1 = {
        color: "#ff1100",
        weight: 3
    };
    var rect2 = {
        color: "#0000AA",
        weight: 1,
        opacity: 0,
        fillOpacity: 0
    };
    var miniMap = new L.Control.MiniMap(osm2, {
        toggleDisplay: true,
        position: "bottomright",
        aimingRectOptions: rect1,
        shadowRectOptions: rect2
    }).addTo(map);

    L.Control.geocoder({
        position: "topleft",
        collapsed: true
    }).addTo(map);

    /* GPS enabled geolocation control set to follow the user's location */
    var locateControl = L.control.locate({
        position: "topleft",
        drawCircle: true,
        follow: true,
        setView: true,
        keepCurrentZoomLevel: true,
        markerStyle: {
            weight: 1,
            opacity: 0.8,
            fillOpacity: 0.8
        },
        circleStyle: {
            weight: 1,
            clickable: false
        },
        icon: "fa fa-location-arrow",
        metric: false,
        strings: {
            title: "My location",
            popup: "You are within {distance} {unit} from this point",
            outsideMapBoundsMsg: "You seem located outside the boundaries of the map"
        },
        locateOptions: {
            maxZoom: 18,
            watch: true,
            enableHighAccuracy: true,
            maximumAge: 10000,
            timeout: 10000
        }
    }).addTo(map);

    var zoom_bar = new L.Control.ZoomBar({
        position: 'topleft'
    }).addTo(map);

    L.control.coordinates({
        position: "bottomleft",
        decimals: 2,
        decimalSeperator: ",",
        labelTemplateLat: "Latitude: {y}",
        labelTemplateLng: "Longitude: {x}"
    }).addTo(map);
    /* scala */
    L.control.scale({
        metric: true,
        position: "bottomleft"
    }).addTo(map);

    var north = L.control({
        position: "bottomleft"
    });
    north.onAdd = function(map) {
        var div = L.DomUtil.create("div", "info legend");
        div.innerHTML = '<img src="<?= base_url() ?>assets/arahmataangin.png"style=width:200px;>';
        return div;
    }
    north.addTo(map);

    $.getJSON("<?= base_url() ?>assets/titik-warmad.geojson", function(data) {
        console.log(data);
        var ratIcon = L.icon({
            iconUrl: '<?= base_url() ?>assets/marker1.png',
            iconSize: [10, 10]
        });
        L.geoJson(data, {
            pointToLayer: function(feature, latlng) {
                var marker = L.marker(latlng, {
                    icon: ratIcon
                });

                console.log("Nilai Y:", feature.properties.Y);
                console.log("Nilai X:", feature.properties.X);

                var popupContent = `
                <div style="text-align: center;">
                    <h5 style="font-weight: bold;">${feature.properties.Nama}</h5>
                    <img src="<?= base_url() ?>assets/images/${feature.properties.Gambar}.jpg" 
                         alt="${feature.properties.Nama}" 
                         style="width: auto; height: 120px; margin: 5px 0;">
                    <p><strong>Alamat:</strong> ${feature.properties.alamat}</p>
                    <p><strong>Keterangan:</strong> ${feature.properties.keterangan}</p>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=${feature.properties.Y},${feature.properties.X}"
                        target="_blank" style="display: inline-block; padding: 8px 15px; background-color: #FFBE00; color: #fff; 
                        text-decoration: none; border-radius: 5px;">
                        Lihat Rute
                        </a>
                </div>
            `;

                marker.bindPopup(popupContent, {
                    maxWidth: 200,
                    minWidth: 100,
                });
                return marker;
            }
        }).addTo(warmad);
    });

    $.getJSON("<?= base_url() ?>/assets/kelurahan-ciputim.geojson", function(KODE) {
        L.geoJson(KODE, {
            style: function(feature) {
                var fillColor,
                    KODE = feature.properties.KODE;
                if (KODE === 6) fillColor = "#2f2f2f";
                else if (KODE === 5) fillColor = "#696969"
                else if (KODE === 4) fillColor = "#808080"
                else if (KODE === 3) fillColor = "#a9a9a9";
                else if (KODE === 2) fillColor = "#c0c0c0";
                else if (KODE === 1) fillColor = "#d3d3d3";
                else if (KODE === 0) fillColor = "#dcdcdc";
                else fillColor = "#f7f7f7"; // no data
                return {
                    weight: 1,
                    fillColor: fillColor,
                    fillOpacity: .6
                };
            },
            onEachFeature: function(feature, layer) {
                layer.bindPopup(feature.properties.NAME_4)
            }
        }).addTo(kelurahan);
    });

    $.getJSON("<?= base_url() ?>/assets/jalan-ciputim.geojson", function(kode) {
        L.geoJson(kode, {
            style: function(feature) {
                var color,
                    kode = feature.properties.kode;
                if (kode < 2) color = "#f2051d";
                else if (kode > 0) color = "#f2051d";
                else color = "#f2051d"; // no data
                return {
                    color: "#999",
                    weight: 2,
                    color: color,
                    fillOpacity: .8
                };
            },
            onEachFeature: function(feature, layer) {
                layer.bindPopup()
            }
        }).addTo(jalan);
    });
</script>