//find store map
function initializeFindStoreMap(stores, markerPath) {
    var mapCanvas = document.getElementById('find-store-map-canvas');

    //map options
    var mapOptions = {
        center: new google.maps.LatLng(56.536308, 41.327157),
        zoom: 7,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    //init the map
    var map = new google.maps.Map(mapCanvas, mapOptions);
    var markers = [];

    for (var i = 0; i < stores.length; i++) {
        var store = stores[i];
        var point = new google.maps.Marker({
            position: new google.maps.LatLng(store.latitude, store.longitude),
            map: map,
            icon: markerPath,
            animation: google.maps.Animation.DROP,
            title: store.address,
            pointId: i
        });

        var infoWindow = new google.maps.InfoWindow({
            content: '<div style="line-height:1.35;overflow:hidden;white-space:nowrap;"><h4>'+store.address+'</h4><p>'+store.storeType+'</p><p>'+store.contact+'</p><p>'+store.schedule+'</p><p></p><a href="'+store.uri+'">Подробнее</a></p></div>'
        });

        markers[i] = {
            point: point,
            info: infoWindow
        }

        point = markers[i].point;
        infoWindow = markers[i].infoWindow;

        google.maps.event.addListener(point, 'click', function(){
            markers[this.pointId].info.open(map, this);
        });
    }

    google.maps.event.addListener(map, 'bounds_changed', function() {
        google.maps.event.trigger(map, 'resize');
    });

    return map;

}

//find store map
function initializeBigMap(stores, markerPath) {
    var vladimirSotresPointId = 99;

    var mapCanvas = document.getElementById('map-canvas');

    //map options
    var mapOptions = {
        center: new google.maps.LatLng(56.536308, 41.327157),
        zoom: 7,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        scrollwheel: false
    }

    //init the map
    var map = new google.maps.Map(mapCanvas, mapOptions);
    var markers = [];

    for (var i = 0; i < stores.length; i++) {
        var store = stores[i];
        var point = new google.maps.Marker({
            position: new google.maps.LatLng(store.latitude, store.longitude),
            map: map,
            icon: markerPath,
            animation: google.maps.Animation.DROP,
            title: store.address,
            pointId: store.id,
            index: i
        });

        //filter out vladimir stores
        if ((store.latitude > 56 && store.latitude < 57) && (store.longitude > 40 && store.longitude < 41)) {
            point.vladimir = true;
            point.setVisible(false);
        } else {
            point.vladimir = false;
        }

        var infoWindow = new google.maps.InfoWindow({
            content: '<div style="line-height:1.35;overflow:hidden;white-space:nowrap;"><h4>'+store.address+'</h4><p>'+store.storeType+'</p></p><p>'+store.contact+'</p><p>'+store.schedule+'</p><p><a href="'+store.link+'">Подробнее</a></p></div>'
        });

        markers[i] = {
            point: point,
            info: infoWindow
        }

        point = markers[i].point;
        infoWindow = markers[i].infoWindow;

        google.maps.event.addListener(point, 'click', function(){
            for (var i = 0; i < markers.length; i++) {
                if (markers[i].info) {
                    markers[i].info.close();
                }
            }
            markers[this.index].info.open(map, this);
        });

        if (i == stores.length - 1) {
            //pseudo point for vladimir
            var point = new google.maps.Marker({
                position: new google.maps.LatLng(56.146122, 40.402363),
                map: map,
                icon: markerPath,
                animation: google.maps.Animation.DROP,
                title: 'Владимир (6 магазинов)',
                pointId: 99,
                index: i+1
            });

            markers[i+1] = {
                point: point,
                info: infoWindow
            }

            point = markers[i+1].point;
            infoWindow = markers[i+1].infoWindow;

            google.maps.event.addListener(point, 'click', function(){
                showStoreOnMap(
                    vladimirSotresPointId,
                    {
                        map: map,
                        markers: markers
                    },
                    12,
                    false
                )
            });

        }
        window.markers = markers;
    }

    google.maps.event.addListener(map, 'bounds_changed', function() {
        google.maps.event.trigger(map, 'resize');
    });

    google.maps.event.addListener(map, 'zoom_changed', function(){
        var zoomLevel = map.getZoom();
        if (zoomLevel < 11) {
            //create one big point in vladimir
            for (var i = 0; i < markers.length; i++) {
               if (markers[i].point.vladimir) {
                   markers[i].point.setVisible(false);
               }
                if (markers[i].point.pointId == 99) {
                   markers[i].point.setVisible(true);
               }
            }
        } else {
            for (var i = 0; i < markers.length; i++) {
                if (markers[i].point.vladimir) {
                    markers[i].point.setVisible(true);
                }
                if (markers[i].point.pointId == 99) {
                    markers[i].point.setVisible(false);
                }
            }
        }
    });

    return {
        map: map,
        markers: markers
    };

}

function showStoreOnMap(storeId, mapInfo, zoomLevel, click) {

    zoomLevel = typeof zoomLevel !== 'undefined' ? zoomLevel : 15;
    click = typeof click !== 'undefined' ? click: true

    if (!mapInfo.map) {
        alert('Map is not available');
        return;
    }
    for (var i = 0; i < mapInfo.markers.length; i++) {
        if (mapInfo.markers[i].point.pointId == storeId) {
            mapInfo.map.panTo(mapInfo.markers[i].point.getPosition());
            mapInfo.map.setZoom(zoomLevel);
            if (click) {
                new google.maps.event.trigger(mapInfo.markers[i].point, 'click');
                var body = $("html, body");
                body.animate({scrollTop:0}, '500', 'swing');
            }
        }
    }
}