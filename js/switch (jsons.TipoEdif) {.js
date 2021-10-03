switch (jsons.TipoEdif) {
    case 'Edifício Público':
        var myIcon = L.icon({
            iconSize: [30, 48],
            iconAnchor: [15, 48],
            popupAnchor: [-7, -45],
        });
        myIcon = L.icon ({iconUrl :"img/Icon_P.png"});
        L.marker([jsons.CoordLongEdif, jsons.CoordLatEdif], {
            icon : myIcon,
        }).bindPopup(divPopup)
        markersPublico.push(marker);
    break;
    case 'Edifício Residencial':
        var myIcon = L.icon({
            iconSize: [30, 48],
            iconAnchor: [15, 48],
            popupAnchor: [-7, -45],
            });
        myIcon = L.icon ({iconUrl :"img/Icon_R.png"});
        L.marker([jsons.CoordLongEdif, jsons.CoordLatEdif], {
            icon : myIcon,
        }).bindPopup(divPopup);
        markersResidencial.push(marker);
    break;
    }
    groupP = L.layerGroup(markersPublico);
    groupR = L.layerGroup(markersResidencial);
    markersPublico.addTo(mymap);
    markersResidencial.addTo(mymap);

//função que mostra apenas os markers da checkbox selecionada
$('#checkboxP').change(function() {
    if (this.checked)
      map.addLayer(groupP);
    else
      map.removeLayer(groupP);
});