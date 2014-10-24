//find store map
function initializeFindStoreMap(stores) {
    var mapCanvas = document.getElementById('find-store-map-canvas');

    //map options
    var mapOptions = {
        center: new google.maps.LatLng(56.093126, 40.360195),
        zoom: 8,
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
            animation: google.maps.Animation.DROP,
            title: store.address,
            pointId: i
        });

        var infoWindow = new google.maps.InfoWindow({
            content: '<div style="line-height:1.35;overflow:hidden;white-space:nowrap;"><h4>'+store.address+'</h4><p>'+store.contact+'</p><p>'+store.schedule+'</p><p></p><a href="'+store.uri+'">Подробнее</a></p></div>'
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
function initializeBigMap(stores) {
    var mapCanvas = document.getElementById('map-canvas');

    //map options
    var mapOptions = {
        center: new google.maps.LatLng(56.093126, 40.360195),
        zoom: 8,
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
            animation: google.maps.Animation.DROP,
            title: store.address,
            pointId: i
        });

        var infoWindow = new google.maps.InfoWindow({
            content: '<div style="line-height:1.35;overflow:hidden;white-space:nowrap;"><h4>'+store.address+'</h4><p>'+store.contact+'</p><p>'+store.schedule+'</p><p><a href="'+store.link+'">Подробнее</a></p></div>'
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