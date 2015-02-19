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

function getFitZoom() {
    var screenHeight = screen.availHeight;

    var zoomLevel = 7;

    if (screenHeight > 800 && screenHeight <= 1400) {
        zoomLevel = 8;
    } else if (screenHeight > 1400) {
        zoomLevel = 9;
    }

    return zoomLevel;

}

function initializeBigMap(stores, markerPath, createFakePoints, wholesaleStore) {

    var initZoomLevel = getFitZoom();

    var vladimirSotresPointId = 99;
    var ivanovoStoresPointId = 98;
    var kovrovStoresPointId = 97;

    var mapCanvas = document.getElementById('map-canvas');

    //map options
    var mapOptions = {
        center: new google.maps.LatLng(56.536308, 41.327157),
        zoom: initZoomLevel,
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

        if (createFakePoints) {
            //filter out vladimir and ivanovo stores
            if ((store.latitude > 56 && store.latitude < 57) && (store.longitude > 40 && store.longitude < 41)) {
                point.vladimir = true;
                point.ivanovo = false;
                point.kovrov = false;
                point.setVisible(false);
            } else if ((store.latitude >= 56.9 && store.latitude <= 57.1) && (store.longitude >= 40.9 && store.longitude <= 41)) {
                point.vladimir = false;
                point.ivanovo = true;
                point.kovrov = false;
                point.setVisible(false);
            } else if ((store.latitude >= 56.3 && store.latitude <= 56.4) && (store.longitude >= 41.3 && store.longitude <= 41.35)) {
                point.vladimir = false;
                point.ivanovo = false;
                point.kovrov = true;
                point.setVisible(false);
            } else {
                point.vladimir = false;
                point.ivanovo = false;
                point.kovrov = false;
            }
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

        if (createFakePoints) {
            if (i == stores.length - 1) {
                //pseudo point for vladimir
                var title = '6 ';
                if (wholesaleStore) {
                    title = '1 служба сбыта и 5 столов заказов'
                } else {
                    title = title + 'магазинов'
                }
                var point = new google.maps.Marker({
                    position: new google.maps.LatLng(56.146122, 40.402363),
                    map: map,
                    icon: markerPath,
                    animation: google.maps.Animation.DROP,
                    title: title,
                    pointId: 99,
                    index: i + 1
                });

                markers[i + 1] = {
                    point: point,
                    info: infoWindow
                }

                point = markers[i + 1].point;
                infoWindow = markers[i + 1].infoWindow;

                google.maps.event.addListener(point, 'click', function () {
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

                //pseudo point for ivanovo
                title = '3 ';
                if (wholesaleStore) {
                    title = title + 'стола заказов'
                } else {
                    title = title + 'магазина'
                }
                var ivanovoPoint = new google.maps.Marker({
                    position: new google.maps.LatLng(56.996917, 40.976612),
                    map: map,
                    icon: markerPath,
                    animation: google.maps.Animation.DROP,
                    title: title,
                    pointId: 98,
                    index: i + 2
                });

                markers[i + 2] = {
                    point: ivanovoPoint,
                    info: infoWindow
                }

                ivanovoPoint = markers[i + 2].point;
                infoWindow = markers[i + 2].infoWindow;

                google.maps.event.addListener(ivanovoPoint, 'click', function () {
                    showStoreOnMap(
                        ivanovoStoresPointId,
                        {
                            map: map,
                            markers: markers
                        },
                        12,
                        false
                    )
                });

                //pseudo point for kovrov
                if (wholesaleStore) {
                    title = '1 служба сбыта и 1 стол заказов'
                } else {
                    title = '2 магазина'
                }
                var kovrovPoint = new google.maps.Marker({
                    position: new google.maps.LatLng(56.360132, 41.311626),
                    map: map,
                    icon: markerPath,
                    animation: google.maps.Animation.DROP,
                    title: title,
                    pointId: kovrovStoresPointId,
                    index: i + 3
                });

                markers[i + 3] = {
                    point: kovrovPoint,
                    info: infoWindow
                }

                kovrovPoint = markers[i + 3].point;
                infoWindow = markers[i + 3].infoWindow;

                google.maps.event.addListener(kovrovPoint, 'click', function () {
                    showStoreOnMap(
                        kovrovStoresPointId,
                        {
                            map: map,
                            markers: markers
                        },
                        12,
                        false
                    )
                });
            }
        }
        window.markers = markers;
    }

    google.maps.event.addListener(map, 'bounds_changed', function() {
        google.maps.event.trigger(map, 'resize');
    });

    google.maps.event.addListener(map, 'zoom_changed', function(){
        if (createFakePoints) {
            var zoomLevel = map.getZoom();
            if (zoomLevel < 11) {
                //create one big point in vladimir
                for (var i = 0; i < markers.length; i++) {
                    if (markers[i].point.vladimir || markers[i].point.ivanovo || markers[i].point.kovrov) {
                        markers[i].point.setVisible(false);
                    }
                    if (markers[i].point.pointId == vladimirSotresPointId || markers[i].point.pointId == ivanovoStoresPointId || markers[i].point.pointId == kovrovStoresPointId) {
                        markers[i].point.setVisible(true);
                    }
                }
            } else {
                for (var i = 0; i < markers.length; i++) {
                    if (markers[i].point.vladimir || markers[i].point.ivanovo || markers[i].point.kovrov) {
                        markers[i].point.setVisible(true);
                    }
                    if (markers[i].point.pointId == vladimirSotresPointId || markers[i].point.pointId == ivanovoStoresPointId || markers[i].point.pointId == kovrovStoresPointId) {
                        markers[i].point.setVisible(false);
                    }
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

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return "";
}