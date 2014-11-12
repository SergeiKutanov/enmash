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
            markers[this.index].info.open(map, this);
        });
    }

    google.maps.event.addListener(map, 'bounds_changed', function() {
        google.maps.event.trigger(map, 'resize');
    });

    return {
        map: map,
        markers: markers
    };

}

function showStoreOnMap(storeId, mapInfo) {

    if (!mapInfo.map) {
        alert('Map is not available');
        return;
    }
    for (var i = 0; i < mapInfo.markers.length; i++) {
        if (mapInfo.markers[i].point.pointId == storeId) {
            mapInfo.map.panTo(mapInfo.markers[i].point.getPosition());
            mapInfo.map.setZoom(15);
            new google.maps.event.trigger(mapInfo.markers[i].point, 'click');
            var body = $("html, body");
            body.animate({scrollTop:0}, '500', 'swing');
        }
    }
}