var autores = []; // para guardar os autores
var app = {
	// Application Constructor
	initialize: function () {
		document.addEventListener("deviceready", this.onDeviceReady.bind(this), false
		);
		getPoints();
	},

	// deviceready Event Handler
	//
	// Bind any cordova events here. Common events are:
	// 'pause', 'resume', etc.

	onDeviceReady: function () {
		this.receivedEvent("deviceready");

		var onSuccess = function (position) {
			var topo = document.getElementById("topo");
			var body = document.body;
			body.classList.add("overflow");

			//buscar o div criado no html
			var mapa = document.getElementById("mapid");
			//criar o mapa atraves da biblioteca da leaflet com a posiçao definida e zoom
			var mymap = L.map("mapid", { zoomControl: false }).setView([39.60360511, -8.40795278], 16);
			
			document.addEventListener("online", onOnline, false);
			var escolheLing = function (option) {
				switch (option) {
					case "EN":
						lang="2";
						return lang;
					break;
					default:
						lang="1";
						return lang;
					break;
				}
			};
			$('#linguagem').change(function(){
				var option = $(this).find('option:selected').val();
				lang = escolheLing(option);
			});

			function onOnline() {
				L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
					maxZoom: 19,
					minZoom: 15,
					attribution:
						'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
				}).addTo(mymap);
			}
			onOnline();
			
			mymap.on('locationfound', onLocationFound);

			var jsonData;
			var control;
			//buscas do elementos criados no html
			var btPos = document.getElementById("btPosicao");
			var icon = document.getElementById("idIcon");
			var divInfo = document.getElementById("infoAdicional");
			var divFullImg = document.getElementById("fullImg");
			var city = L.layerGroup();
			var polys = L.layerGroup();
			//metodo de jQuery para ir buscar e ler o ficheiro info.json
			getEdificios("1");

			function ecraImagem(imgP) {
                divFullImg.classList.remove('hidden');
                //é criado um canvas com a ajuda do ficheiro img-touch-canvas.js usar touch gestures no dispositivo
                var gesturableImg = new ImgTouchCanvas({
                    canvas: document.getElementById('mycanvas'),
                    path: "" + imgP + "",
                });
            }

			//funcão para remover painel de direções gerado automaticamente para o trajeto
			function removeRoutingControl() {
				if (control != null) {
					mymap.removeControl(control);
					control = null;
				}
			}

			
			/****************************************************************/

			//trabalha o evento do botao dos dispositivos para voltar atras
			document.addEventListener("backbutton", onBackKeyDown, false);
			function onBackKeyDown(e) {
				//sair da aplicação quando esta está no mapa
				if (mapa.classList.contains("hidden") === false) {
					navigator.app.exitApp();

					/* *************** para sair da imagem ***************  */
				} else if (topo.classList.contains("hidden") === true) {
					window.scrollTo(0, 0);
					document.body.style.background = "#F2F2F2";
					topo.classList.remove("hidden");
					divInfo.classList.remove("hidden");
					divFullImg.classList.add("hidden");

					/******************************************************* */
					/* *********** sair do Sobre para o mapa  *************  */
				} else if (divAcerca.classList.contains("hidden") === false) {
					// imgFull.innerHTML = "";

					divInfo.innerHTML = "";
					e.preventDefault();
					window.scrollTo(0, 0);
					btPos.classList.remove("hidden");
					icon.classList.remove("hidden");
					mapa.classList.remove("hidden");
					divInfo.classList.add("hidden");
					divAcerca.classList.add("hidden");
					body.classList.add("overflow");
					mymap.closePopup();
					/******************************************************* */
				} else {
					/* ***** sair da informação do edificio para o mapa **** */
					//imgFull.innerHTML = "";

					divInfo.innerHTML = "";
					e.preventDefault();
					window.scrollTo(0, 0);
					btPos.classList.remove("hidden");
					icon.classList.remove("hidden");
					mapa.classList.remove("hidden");
					divInfo.classList.add("hidden");
					body.classList.add("overflow");
					mymap.closePopup();
					/******************************************************* */
				}
			}

			setInterval(5000);
			mymap.locate({ setView: true, maxZoom: 16 });

			/* =========  Função para o butão de ir para a posição do marker ===========*/
			$(".refreshButton").on("click", function () {
				mymap.locate({ setView: true, maxZoom: 17 });
			});
			mymap.on("locationfound", onLocationFound);
			function onLocationFound(e) {
			
				// e.heading will contain the user's heading (in degrees) if it's available,
				// and if not it will be NaN. This would allow you to point a marker in the same direction the user is pointed.
				L.marker(e.latlng).addTo(mymap);
			}
	/* ======================================================================== */
	const PMarkers = [];
	const RMarkers = [];

	// Funcao p/ obter toda a informação do edificio.
	function getEdificios() {
		polys.clearLayers();
		$("#rotas").empty();
		$.getJSON( "info.json", function (info) {
			    if (lang=="1")
				jsonData = info[0];
				else
				jsonData = info[1];
				//*******Dropdown Arquitetos *******/
				//Cria as opções da dropdown autores/arquitetos dos edificios
				let autoresAux = [];
				let dropdown = $('#Autores-dropdown');
				dropdown.empty();
				dropdown.append('<option selected="true" disabled>&nbsp;INTERVENINETES DOS PROJETOS&nbsp;&nbsp;v</option>');
				dropdown.prop('selectedIndex', 0);

				//*********************************/

				//let cada posição do ficheiro json e inserir numa variavel
				for (var i = 0; i < jsonData.length; i++) {
					let jsons = jsonData[i];

					//Carrega a lista de Autores/Arquitetos na dropdown
					autoresAux.push(jsons.Autores)
					//****************** Popups ***********************/

					//criação de elementos para mostrar no popup quando se clica num icon ou poligno de um edificio
					var divPopup = document.createElement("div");
					divPopup.setAttribute("id", "iDdivPopup");
					
					var popUpNome = document.createElement("div");
					popUpNome.setAttribute("id", "idPopUpNome");
					popUpNome.classList.add("txt", "item1" );

					var btWaypoint = document.createElement("button");
					btWaypoint.setAttribute("id", "idBtWaypoint");
					btWaypoint.classList.add("btn", "item2");
					
					var IconTraj = document.createElement("img");
					IconTraj.setAttribute("id", "idIconTraj");
					IconTraj.src="../img/Icon_Trajeto.png";
					btWaypoint.prepend(IconTraj);
					
					var greenIcon = new L.Icon({
						iconUrl: "./img/green.png",
						shadowUrl: "../img/shadow.png",
						iconSize: [25, 41],
						iconAnchor: [12, 41],
						popupAnchor: [1, -34],
						shadowSize: [41, 41],
					});
					

					//onclick do trajeto e criação da rota com metodos do leaflet routing map
					//em que vai buscar as coordenadas do user e faz rota ate as coordernadas do icon clicado
					btWaypoint.onclick = waypoint => {

                        removeRoutingControl();
						
                        console.log(current_position._latlng.lat);

                        control = L.Routing.control({
                            waypoints: [
                                L.latLng(current_position._latlng),
                                L.latLng(jsons.IconCoordenadas)
                            ],
                            createMarker: function (i, wp, nWps) {
                                if (i === nWps - 1) {
                                    // here change the starting and ending icons
                                    return L.marker(wp.latLng, {
                                        icon: greenIcon // here pass the custom marker icon instance
                                    });
                                }
                            },
                            lineOptions: {
                                styles: [{ color: 'red', opacity: 1, weight: 5 }],
                            },
                            draggableWaypoints: false,
                        }).addTo(mymap);
                    }

					popUpNome.textContent = jsons.NomeEdificio;
					
					// if (jsons.TipoEdificio == 'Edifício Público' || jsons.TipoEdificio == 'Public Building'){
					// 	var PingP= document.createElement("img");
					// 	PingP.setAttribute("id", "idPingP");
					// 	PingP.src="../img/Icon_P.png";
					// 	popUpNome.prepend(PingP);
					// } else {
					// 	var PingR= document.createElement("img");
					// 	PingR.setAttribute("id", "idPingR");
					// 	PingR.src="../img/Icon_R.png";
					// 	popUpNome.prepend(PingR);
					// }
					
					divPopup.appendChild(popUpNome);

					$(divPopup).prepend($(`<img src="`+jsons.Imagens[0].Path+`" id="imgPopup" class="item4"/>`));

					//criação do icon dos edificios com a devida destinção entre residencial ou publico
					var myIcon = L.icon({
						iconSize: [30, 48],
						iconAnchor: [15, 48],
						popupAnchor: [-7, -45],
					});

					let marker;
					if (jsons.TipoEdificio == 'Edifício Público' || jsons.TipoEdificio == 'Public Building'){
						myIcon = L.icon({ iconUrl: "img/Icon_P.png" })
						marker = L.marker(jsons.IconCoordenadas, {
							icon: myIcon,
						})
						PMarkers.push({
							marker, divPopup
						})
					} else {
						myIcon = L.icon({ iconUrl: "img/Icon_R.png" })
						marker = L.marker(jsons.IconCoordenadas, {
							icon: myIcon,
						})
						RMarkers.push({
							marker, divPopup
						})
					}
					autores.push(jsons.Autores);
					marker.addTo(mymap).bindPopup(divPopup);

					

					//****************Janela de Detalhes***********************
					
					//atraves de jquery clicar no botão detalhes de um edificio e ler as suas informações
					var link = $(
						'<a href="#" class="item3" id="idBtInfo"><img src="../img/Icon_Info.png" id="idIconInfo"/></a>'
					).click(function () {

						// jsons tem toda a informacao sobre o edificio

						body.classList.remove("overflow");
						btPos.classList.add("hidden");
						divInfo.classList.remove("hidden");
						document.getElementById("footer").style.display = "none";
						document.getElementById("btnfiltro").style.display = "none";
						mapa.classList.add("hidden");
						mymap.closePopup();

						//criação de elementos e adicionados ao html

						var pTipoEdificio = document.createElement("h5");
						pTipoEdificio.setAttribute("id", "idTipoEdificio");

						var spanLinha = document.createElement("hr");
						spanLinha.setAttribute("class", "SpanLinha");
						spanLinha.textContent = "";
						
						var pNomeEdificio = document.createElement("h4");
						pNomeEdificio.setAttribute("id", "idNomeEdificio");

						var pAutores = document.createElement("p");
						pAutores.setAttribute("id", "idAutores");

						var pData = document.createElement("p");
						pData.setAttribute("id", "idData");

						var pLocalizacao = document.createElement("p");
						pLocalizacao.setAttribute("id", "idLocalizacao");

						var pDescricao = document.createElement("p");
						pDescricao.setAttribute("id", "idDescricao");

						pTipoEdificio.textContent = jsons.TipoEdificio;
						pNomeEdificio.textContent = jsons.NomeEdificio;
						pLocalizacao.textContent = "Localização: " + jsons.Localizacao;
						pAutores.textContent = jsons.labelAutores + jsons.Autores;
						pDescricao.textContent = jsons.Descricao;
						pData.textContent = jsons.labelData + jsons.Data;

						var pRetorno = $('<a href="#"><img src="../img/setaRetorno.png" class="Retorno" id="idRetorno"/></a>').click(function () {
							divInfo.classList.add("hidden");
							mapa.classList.remove("hidden");
							body.classList.add("overflow");
							btPos.classList.remove("hidden");
							document.getElementById("footer").style.display = "block";
							document.getElementById("btnfiltro").style.display = "block";
							mymap.closePopup();
						})[0];
						divInfo.appendChild(pRetorno);

						$(divInfo).prepend($(`<img src="`+jsons.Imagens[0].Path+`" id="imgCabecalho"/>`));
						if (jsons.TipoEdificio == 'Edifício Público' || jsons.TipoEdificio == 'Public Building') {
							var LogoP= document.createElement("img");
							LogoP.setAttribute("id", "idLogoP");
							LogoP.src="../img/LogoP.png";
							spanLinha.style.borderTop="3px solid #FA6980";
							pTipoEdificio.prepend(LogoP);
							divInfo.appendChild(pTipoEdificio).style.color = '#FA6980';
							var PingP= document.createElement("img");
							PingP.setAttribute("id", "idPingP");
							PingP.src="../img/Icon_P.png";
							pLocalizacao.prepend(PingP);
						} else {
							var LogoR= document.createElement("img");
							LogoR.setAttribute("id", "idLogoR");
							LogoR.src="../img/LogoR.png";
							spanLinha.style.borderTop="3px solid #5773FF";
							pTipoEdificio.prepend(LogoR);
							divInfo.appendChild(pTipoEdificio).style.color = '#5773FF';
							var PingR= document.createElement("img");
							PingR.setAttribute("id", "idPingR");
							PingR.src="../img/Icon_R.png";
							pLocalizacao.prepend(PingR);
						}
						
						divInfo.appendChild(spanLinha);
						divInfo.appendChild(pNomeEdificio);
						divInfo.appendChild(pAutores);
						divInfo.appendChild(pData);
						divInfo.appendChild(pLocalizacao);
						divInfo.appendChild(pDescricao);

						//Imagens
						var divRow = document.createElement("div");
						divRow.setAttribute("id", "idDivRow");
						divRow.setAttribute("class", "row");

						for (var j = 0; j < jsons.Imagens.length; j++) {
							
							var imgEdificio = jsons.Imagens[j];

							var divColMd = document.createElement('div');
							divColMd.setAttribute('id', 'idDivColMd');
							divColMd.setAttribute('class', 'col-md-4');
							divColMd.setAttribute('class', 'content');
							var divThumb = document.createElement('div');
							divThumb.setAttribute('class', 'thumbnail');
							divThumb.setAttribute('id', 'idDivThumb');
							var divCaption = document.createElement('div')
							divCaption.setAttribute('id', 'idDivCaption');
							divCaption.setAttribute('class', 'caption');
							divCaption.setAttribute('class', 'rounded-bottom');

							var img = document.createElement('img');
							var imgLegenda = document.createElement('p');
							var imgAutor = document.createElement('p');

							//lida a path da imagem para a pasta das imagens
							img.src = imgEdificio.Path;
							img.setAttribute('id', 'idImagens');
							img.setAttribute('class', 'rounded');

							//onclick na imagem para ver esta com mais zoom que é mostrada inicialmente
							img.setAttribute('data-Path', imgEdificio);
							img.onclick = fullImg => {
								var pathId = fullImg.target.getAttribute('data-Path', imgEdificio);
								//é chamada a fução de abrir a imagem, função essa que leva como parametro o path da imagem
								ecraImagem(pathId);
							}
							//atribuição dos valores existentes no json
							imgLegenda.textContent = imgEdificio.Legenda;
							imgAutor.textContent = imgEdificio.AutorFonte;

							// divSlideshow.appendChild(img);
							// divSlideshow.appendChild(imgAutor);
							// divSlideshow.appendChild(imgLegenda);
							divCaption.appendChild(imgLegenda);
							divCaption.appendChild(imgAutor);
							divThumb.appendChild(img);
							divThumb.appendChild(divCaption);
							divColMd.appendChild(divThumb);
							divRow.appendChild(divColMd);
							divInfo.appendChild(divRow);
						};
					})[0];

					if (jsons.TipoEdificio == 'Edifício Público' || jsons.TipoEdificio == 'Public Building'){
						divPopup.appendChild(btWaypoint).style.backgroundColor = '#FA6980';
						divPopup.appendChild(link).style.backgroundColor = '#FA6980';
						divPopup.appendChild(link).style.Color = 'white';
					} else {
						divPopup.appendChild(btWaypoint).style.backgroundColor = '#5773FF';
						divPopup.appendChild(link).style.backgroundColor = '#5773FF';
						divPopup.appendChild(link).style.Color = 'white';
					}
					marker.bindPopup(divPopup);

					//************Rotas *****************/
					const rt = jsons.Rotas;
					//console.log(rt);
					const listaRotas = document.querySelector("#rotas");
					$.each(rt, (i, rout) => {
						const novaRota = document.createElement("a");
						novaRota.textContent = rout.nome;
						novaRota.rel = "modal:close";
						novaRota.classList.add("btn");
						novaRota.classList.add("btn-block");
						novaRota.classList.add("btn-warning");
						novaRota.addEventListener("click", () => {
							$(".leaflet-marker-icon").remove();
							$(".leaflet-popup").remove();
							mymap.locate({ setView: true, maxZoom: 16 });
						});
						listaRotas.appendChild(novaRota);

						let routeCoord;
						const pivot = rout.pivot;
						$.each(pivot, (i, p) => {
							$.each(jsonData, (i, d) => {
								novaRota.addEventListener("click", () => {
									if (p.id_Edificio == d.idEdificio) {
										//console.log(d.NomeEdif);
										routeCoord = L.marker([d.CoordLongEdif, d.CoordLatEdif], {
											icon: myIcon,
										})
											.addTo(city)
											.bindPopup(getEdificios(d));
										city.addTo(mymap);
									}
								});
							});
						});
					});
				}

				// Popula a dropdown dos autores com a clausula de não permitir duplicados
				const newAutores = autores.reduce((acc, el) => {
					const autores = el;
					acc.push(...autores);
					return acc;
				}, []).filter((value, index, self) => self.indexOf(value) === index)
				for (var i = 0; i < newAutores.length; i++) {
					dropdown.append($('<option value="' + newAutores[i] + '">' + newAutores[i] +'</option>'));
				}

				var AutoresEdif = jsonData[i].Autores;
			}
		).fail(function (jqXHR, textStatus, errorThrown) { alert(textStatus + " " + errorThrown) });
	}

	//*******************Pings Selecionados************************

	//função que mostra apenas os pings da checkbox selecionada
	$('#checkboxR').change(function () {
		if (this.checked) {
			RMarkers.forEach(({ marker, divPopup }) => marker.addTo(mymap).bindPopup(divPopup))

		}
		else {
			RMarkers.forEach(({ marker}) => mymap.removeLayer(marker))
		}
	})

	$('#checkboxP').change(function () {
		if (this.checked) {
			PMarkers.forEach(({ marker, divPopup }) => marker.addTo(mymap).bindPopup(divPopup))
		}
		else {
			PMarkers.forEach(({ marker}) => mymap.removeLayer(marker))
		}
	});
	const MarkersPR = [PMarkers,RMarkers];
	$('#Autores-dropdown').change(function () {
		if ($(this).selected  == AutoresEdif[i] ) {
			MarkersPR.forEach(({ marker, divPopup }) => marker.addTo(mymap).bindPopup(divPopup))
		} 
		else {
			MarkersPR.forEach(({ marker})  => mymap.removeLayer(marker))
		}
	});

	//função que mostra apenas os pings do respetivo autor/arquiteto selecionado (não funcional)
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// placeholders for the L.marker and L.circle representing user's current position and accuracy
	var current_position;

	//funcoes que vao ler a posição do utilizador e marcar no mapa e remover a posição anterior
	function onLocationFound(e) {
		// if position defined, then remove the existing position marker and accuracy circle from the map
		if (current_position) {
			mymap.removeLayer(current_position);
		}
		current_position = L.marker(e.latlng).addTo(mymap);

		if (e.latitude < 39.620443 && e.latitude > 39.59430 && e.longitude < -8.426837 && e.longitude > -8.374149)
			current_position = L.marker(e.latlng).addTo(mymap);
		else {
			e.latlng.lat = 39.60360511;
			e.latlng.lng = -8.40795278;
			current_position = L.marker(e.latlng).addTo(mymap);
		}

		mymap.setView(new L.LatLng(e.latlng.lat, e.latlng.lng), 16);
	}

	function onLocationError(e) {
		alert(e.message);
	}

	mymap.on("locationfound", onLocationFound);

}

	var onError = function (error) {
		str = "code: " + error.code + "\n" + "message: " + error.message + "\n";
		if (error.code == 3) {
			// no gps
			str = "Sem sinal de GPS.";
			onSuccess();
		}
		alert(str);
	}

	navigator.geolocation.getCurrentPosition(onSuccess, onError, { timeout: 10000, enableHighAccuracy: true, });
		},

	// Update DOM on a Received Event
	receivedEvent: function (id) {
		var parentElement = document.getElementById(id);
	}
};

////////////////////////////////////////////////////////////////////////////////
//* PontosPage */

var escolheLing = function (option) {
	switch (option) {
		case "EN":
		  lang="2";
		  return lang;
		  break;
		default:
			lang="1";
			return lang;
		  break;
	}
};

$('#linguagem').change(function(){
	var option = $(this).find('option:selected').val();
	lang = escolheLing(option);
});

function getPoints() {
	$.getJSON("info.json", function (info) {
		if (lang=="1")
		jsonData = info[0];
		else
		jsonData = info[1];
			var PP = document.getElementById("PP");
			if (PP) {
				$.each(jsonData, function (index, element) {
					PP.appendChild(listPoint(element));
				});
			}
		}
	);
}

function listPoint(jsons) {
	var ponto = document.createElement("div");
	ponto.classList.add("ponto");
	ponto.setAttribute("id", "iDponto");
	var tipoEdif = document.createElement("p");
	tipoEdif.setAttribute("id", "iDTipo");
	tipoEdif.classList.add("ponto4");
	var nome = document.createElement("p");
	nome.setAttribute("id", "iDnome");
	nome.classList.add("ponto2");
	nome.textContent = jsons.NomeEdificio;
	tipoEdif.textContent = jsons.TipoEdificio;

	//*******Dropdown Arquitetos *******/
	//Cria as opções da dropdown autores/arquitetos dos edificios
	let autoresAux = [];
	let dropdown = $('#Autores-dropdown');
	dropdown.empty();
	dropdown.append('<option selected="true" disabled>&nbsp;INTERVENINETES DOS PROJETOS&nbsp;&nbsp;v</option>');
	dropdown.prop('selectedIndex', 0);

	//Carrega a lista de Autores/Arquitetos na dropdown
	autoresAux.push(jsons.Autores);
	autores.push(jsons.Autores);

	// Popula a dropdown dos autores com a clausula de não permitir duplicados
	const newAutores = autoresAux.reduce((acc, el) => {
		const autores = el;
		acc.push(...autores);
		return acc;
	}, []).filter((value, index, self) => self.indexOf(value) === index)
	for (var i = 0; i < newAutores.length; i++) {
		dropdown.append($('<option value="' + newAutores[i] + '">' + newAutores[i] + '</option>'));
	}

	var divInfo = document.getElementById("infoAdicional");

	//****************Janela de Detalhes***********************
	//atraves de jquery clicar no botão detalhes de um edificio e ler as suas informações
	var link = $(
		'<a href="#" id="btnLink" class="ponto3" style=" height:35px; width:35px; color: white; text-align: center; margin-bottom: .5em; margin-left: .5em; padding: .75em; text-decoration: none; border-radius: .25rem; "><img src="../img/seta.png"  id="idBtnPP"/></a>'
	).click(function () {
		// class="speciallink badge badge-info" margin-left: 0.7em; margin-right: -10em;

		// jsons tem toda a informacao sobre o edificio
		divInfo.classList.remove("hidden");
		document.getElementById("btnfiltro").style.display = "none";
		document.getElementById("footer").style.display = "none";
		document.getElementById("PP").style.display = "none";

		//criação de elementos e adicionados ao html

		var pTipoEdificio = document.createElement("h5");
		pTipoEdificio.setAttribute("id", "idTipoEdificio");

		var spanLinha = document.createElement("hr");
		spanLinha.setAttribute("class", "SpanLinha");
		spanLinha.textContent = "";
		
		var pNomeEdificio = document.createElement("h4");
		pNomeEdificio.setAttribute("id", "idNomeEdificio");

		var pAutores = document.createElement("p");
		pAutores.setAttribute("id", "idAutores");

		var pData = document.createElement("p");
		pData.setAttribute("id", "idData");

		var pLocalizacao = document.createElement("p");
		pLocalizacao.setAttribute("id", "idLocalizacao");

		var pDescricao = document.createElement("p");
		pDescricao.setAttribute("id", "idDescricao");

		pTipoEdificio.textContent = jsons.TipoEdificio;
		pNomeEdificio.textContent = jsons.NomeEdificio;
		pLocalizacao.textContent = "Localização: " + jsons.Localizacao;
		pAutores.textContent = jsons.labelAutores + jsons.Autores;
		pDescricao.textContent = jsons.Descricao;
		pData.textContent = jsons.labelData + jsons.Data;

		var pRetorno = $('<a href="#"><img src="../img/setaRetorno.png" class="Retorno" id="idRetorno"/></a>').click(function () {
			divInfo.classList.add("hidden");
			document.getElementById("PP").style.display = "block";
		})[0];
		divInfo.appendChild(pRetorno);

		$(divInfo).prepend($(`<img src="`+jsons.Imagens[0].Path+`" id="imgCabecalho"/>`));
		if (jsons.TipoEdificio == 'Edifício Público' || jsons.TipoEdificio == 'Public Building') {
			var LogoP= document.createElement("img");
			LogoP.setAttribute("id", "idLogoP");
			LogoP.src="../img/LogoP.png";
			spanLinha.style.borderTop="3px solid #FA6980";
			pTipoEdificio.prepend(LogoP);
			divInfo.appendChild(pTipoEdificio).style.color = '#FA6980';
			var PingP= document.createElement("img");
			PingP.setAttribute("id", "idPingP");
			PingP.src="../img/Icon_P.png";
			pLocalizacao.prepend(PingP);
		} else {
			var LogoR= document.createElement("img");
			LogoR.setAttribute("id", "idLogoR");
			LogoR.src="../img/LogoR.png";
			spanLinha.style.borderTop="3px solid #5773FF";
			pTipoEdificio.prepend(LogoR);
			divInfo.appendChild(pTipoEdificio).style.color = '#5773FF';
			var PingR= document.createElement("img");
			PingR.setAttribute("id", "idPingR");
			PingR.src="../img/Icon_R.png";
			pLocalizacao.prepend(PingR);
		}
		
		divInfo.appendChild(spanLinha);
		divInfo.appendChild(pNomeEdificio);
		divInfo.appendChild(pAutores);
		divInfo.appendChild(pData);
		divInfo.appendChild(pLocalizacao);
		divInfo.appendChild(pDescricao);

		//Imagens
		var divRow = document.createElement("div");
		divRow.setAttribute("id", "idDivRow");
		divRow.setAttribute("class", "row");

		//Slideshow Imagens
		
		//ler arrays de imagens existente no json e criar os elementos para cada imagens com a legendas e o autores da imagem
		for (var j = 0; j < jsons.Imagens.length; j++) {
			var imgEdificio = jsons.Imagens[j];

			var carouselCont = document.createElement('div');
			carouselCont.setAttribute('class', 'carousel-container');
			var navigator = document.createElement('div');
			navigator.setAttribute('class', 'navigator');
			var btnPrev = document.createElement('div');
			btnPrev.setAttribute('class', 'prev nav-btn');
			var btnNext = document.createElement('div');
			btnNext.setAttribute('class', 'next nav-btn');
			
			var carousel = document.createElement('div');
			carousel.setAttribute('class', 'carousel');
			var itemMain = document.createElement('div');
			itemMain.setAttribute('class', 'item');
			var item = document.createElement('div');
			item.setAttribute('class', 'item');

			var img = document.createElement('img');
			//lida a path da imagem para a pasta das imagens
			img.src = imgEdificio.Path;
			img.setAttribute('id', 'idImagens');
			img.setAttribute('class', 'rounded');
			
			var imgLegenda = document.createElement('p');
			var imgAutor = document.createElement('p');

			imgLegenda.textContent = imgEdificio.Legenda;
			imgAutor.textContent = imgEdificio.AutorFonte;
			
			const prev = document.querySelector('.prev');
			const next = document.querySelector('.next');
			const images = document.querySelector('.carousel').children;
			const totalImages = images.length;
			let index = 0;
			
			itemMain.appendChild(img[0]);
			item.appendChild(img[i]);
			carousel.appendChild(itemMain, item);
			navigator.appendChild(btnPrev, btnNext);
			carouselCont.appendChild(navigator);
			carouselCont.appendChild(carousel);
			
			

			prev.addEventListener('click', () => {
				nextImage('next');
			})
			next.addEventListener('click', () => {
				nextImage('prev');
			})
			
			function nextImage(direction) {
			if(direction == 'next') {
				index++;  // increase by 1, Global variable
				if(index == totalImages) {
				index = 0;
				}
			} else {
				if(index == 0) {
				index = totalImages - 1;
				} else {
				index--; // Backwards by 1
				}
			}
			
			for(let i = 0; i < images.length; i++) {
				images[i].classList.remove('main');
			}
				images[index].classList.add('main');
			}

			var divColMd = document.createElement('div');
			divColMd.setAttribute('id', 'idDivColMd');
			divColMd.setAttribute('class', 'col-md-4');
			divColMd.setAttribute('class', 'content');
			var divThumb = document.createElement('div');
			divThumb.setAttribute('class', 'thumbnail');
			divThumb.setAttribute('id', 'idDivThumb');
			var divCaption = document.createElement('div')
			divCaption.setAttribute('id', 'idDivCaption');
			divCaption.setAttribute('class', 'caption');
			divCaption.setAttribute('class', 'rounded-bottom');

			//onclick na imagem para ver esta com mais zoom que é mostrada inicialmente
			// img.setAttribute('data-Path', imgEdificio);
			// img.onclick = fullImg => {
			// 	var pathId = fullImg.target.getAttribute('data-Path', imgEdificio);
			// 	//é chamada a fução de abrir a imagem, função essa que leva como parametro o path da imagem
			// 	ecraImagem(pathId);
			// }
			//atribuição dos valores existentes no json

			// divCaption.appendChild(imgLegenda);
			// divCaption.appendChild(imgAutor);
			// divThumb.appendChild(img);
			// divThumb.appendChild(divCaption);
			// divColMd.appendChild(divThumb);
			// divRow.appendChild(divColMd);
			// divInfo.appendChild(divRow);
			divInfo.appendChild(carouselCont);
			
		};
	})[0];

	//*******************Pings Selecionados************************
	$('#checkboxR').change(function () {
		if (this.checked) {

		}
		else {
		}
	})

	$('#checkboxP').change(function () {
		if (this.checked) {
			
		}
		else {
			
		}
	});

	//função que mostra apenas os pings da checkbox selecionada
	if (jsons.TipoEdificio == 'Edifício Público' || jsons.TipoEdificio == 'Public Building') {
		ponto.appendChild(link).style.backgroundColor = '#FA6980';
	} else {
		ponto.appendChild(link).style.backgroundColor = '#5773FF';
	}
	ponto.appendChild(nome);
	$(ponto).prepend($(`<img src="`+jsons.Imagens[0].Path+`"class=ponto1 width=80 height=80 border-radius= "10px 10px 10px 10px" align-self = "center"/>`));
	return ponto;
}

app.initialize();