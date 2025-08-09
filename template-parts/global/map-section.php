<?php
/**
 * Map Section
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$is_contact_page = ! empty( $args['is-contact-page'] ) ? $args['is-contact-page'] : false;
$map_section     = get_field( 'map-section', 'option' );

if ( ! empty( $map_section['map'] ) ) :
	$section_map = $map_section['map'];
	$top_text    = $map_section['top_text'];
	$bottom_text = $map_section['bottom_text'];
	$left_text   = $map_section['left_text'];
	$right_text  = $map_section['right_text'];
	$button      = $map_section['button'];
	?>
	<section id="map" class="section">
		<div class="container">
			<div class="vert vert-center m-str">
				<div class="map-keeper lf-map">
					<div id="yandexMapSpbVoz" class="lf-map__container"></div>
					<?php
					// phpcs:ignore
					//echo $section_map; ?>
					<?php if ( ! empty( $top_text ) ) : ?>
						<div class="map-dot lf-map__text">
							<div class="p-64-64 map-line"><?php echo esc_html( $top_text ); ?></div>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $left_text ) ) : ?>
						<div class="map-dot _2 lf-map__text">
							<div class="p-64-64 map-line"><?php echo esc_html( $left_text ); ?></div>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $right_text ) ) : ?>
						<div class="map-dot _3 lf-map__text">
							<div class="p-64-64 map-line"><?php echo esc_html( $right_text ); ?></div>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $bottom_text ) ) : ?>
						<div class="map-dot _4 lf-map__text">
							<div class="p-64-64 map-line"><?php echo esc_html( $bottom_text ); ?></div>
						</div>
					<?php endif; ?>
				</div>
				<?php if ( ! $is_contact_page && ! empty( $button ) ) : ?>
					<a href="<?php echo esc_url( $button['url'] ); ?>" class="btn pink-btn w-inline-block" target="<?php echo esc_attr( $button['target'] ); ?>">
						<div><?php echo esc_html( $button['title'] ); ?></div>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<script>
		async function initMap() {
	await ymaps3.ready;
	const customization = await (await fetch(`<?php echo get_template_directory_uri() . '/assets/yandex-map.json'; ?>`)).json();
	

	const {YMap, YMapDefaultSchemeLayer, YMapDefaultFeaturesLayer, YMapFeature, YMapComplexEntity, YMapMarker} = ymaps3;
	class CustomMarkerWithPopup extends YMapComplexEntity {
		constructor(options) {
			super(options);
			this._marker = null;
			this._popup = null;

			this._closePopupBodyClickHandler =
			this._closePopupBodyClickHandler.bind(this);
		}

		// Handler for attaching the control to the map
		_onAttach() {
			this._createMarker();
		}
		// Handler for detaching control from the map
		_onDetach() {
			this._marker = null;
			document.body.removeEventListener(
				'click',
				this._closePopupBodyClickHandler
			);
		}
		// Handler for updating marker properties
		_onUpdate(props) {
			if (props.zIndex !== undefined) {
				this._marker?.update({ zIndex: props.zIndex });
			}
			if (props.coordinates !== undefined) {
				this._marker?.update({ coordinates: props.coordinates });
			}
		}
		// Method for creating a marker element
		_createMarker() {
			const element = document.createElement('div');
			element.className = 'marker';

			if (this._props.textMarker) {
				element.style.padding = '5rem 10rem';
				element.style.backgroundColor = '#F22EA9';
				element.style.color = '#fff';
				element.style.fontSize = '14rem';
				element.style.lineHeight = 1;
				element.style.fontWeight = 500;
				element.style.whiteSpace = 'nowrap';
				element.style.translate = '-50% -50%';

				element.textContent = this._props.title;
			} else {
				element.style.width = '26rem';
				element.style.translate = '-50% -100%';
	
				const imgElement = new Image();
				imgElement.src = '<?php echo get_template_directory_uri() . '/images/map-marker.svg'; ?>';
				imgElement.className = 'marker__icon';
				imgElement.width = 27;
				imgElement.height = 46;
	imgElement.style.pointerEvents = 'none';
				element.append(imgElement);
			}
			
			element.onclick = () => {
				this._openPopup();
				map.setLocation({
					center: this._props.coordinates,
					duration: 800,
				});
			};

			this._marker = new YMapMarker(
				{ coordinates: this._props.coordinates },
				element
			);

          this.addChild(this._marker);
          this._openPopup();
		}

		_closePopupBodyClickHandler(event) {
			if (
				!event.target.closest('.popup') &&
				event.target !== this._marker.element
			) {
				this._closePopup();
			}
		}

		// Method for creating a popup window element
		_openPopup() {
			if (this._props.textMarker) {
				return;
			}

			if (this._popup) {
				return;
			}

			this._marker.element.classList.add('marker--selected');

			const element = document.createElement('div');
			element.className = 'lf-popup';
			element.style.cursor = 'pointer';

			/**
			 *
			 * @param {PointerEvent} event
			 */
			element.onclick = (event) => {
				event.preventDefault();

				document.getElementById(element.dataset.popupTrigger)?.click();
			};
			
			const headerElement = document.createElement('header');
			headerElement.className = 'lf-popup__header';
			headerElement.textContent = this._props.title;

			const bodyElement = document.createElement('div');
			bodyElement.className = 'lf-popup__body';
			bodyElement.innerHTML = this._props.address;

          if (this._props.phone) {
            const phoneWrapperElement = document.createElement('div');
            phoneWrapperElement.style.display = 'flex';
            bodyElement.append(phoneWrapperElement);

            const phoneElementId = 'yaMapPhoneNumber';
        const phoneLinkElement = document.createElement('a');
            phoneLinkElement.setAttribute('id', phoneElementId);
            phoneLinkElement.setAttribute('data-js-phone-number', this._props.phone);
				phoneLinkElement.setAttribute('href', `tel:${this._props.phone.replace(/[^\d+]/g, '')}`);
				phoneLinkElement.className = 'lf-popup__phone';
				phoneLinkElement.textContent = this._props.phone.substring(0, 9) + '...';

            phoneWrapperElement.append(phoneLinkElement);

            const showPhoneButton = document.createElement('button');
            showPhoneButton.type = 'button';
            showPhoneButton.className = 'phone-button uppercase';
            showPhoneButton.setAttribute('data-js-phone-number-button', phoneElementId);

            showPhoneButton.innerHTML = '&nbsp;Показать';

            phoneWrapperElement.append(showPhoneButton);
			}

			const workingHoursElement = document.createElement('div');
			workingHoursElement.className = 'lf-popup__hours';
			workingHoursElement.innerHTML = this._props.workingHours;

			bodyElement.append(workingHoursElement);

			element.append(headerElement);
			element.append(bodyElement);

			// document.body.addEventListener(
			// 	'click',
			// 	this._closePopupBodyClickHandler
			// );

			const zIndex =
				(this._props.zIndex ?? YMapMarker.defaultProps.zIndex) + 1_000;
			this._popup = new YMapMarker(
				{
					coordinates: this._props.coordinates,
					zIndex,
					// This allows you to scroll over popup
					blockBehaviors: this._props.blockBehaviors,
				},
				element
			);
			this.addChild(this._popup);
		}

		_closePopup() {
			if (!this._popup) {
				return;
			}

			this.removeChild(this._popup);
			this._popup = null;
			this._marker?.element?.classList.remove('marker--selected');
		}
	}

	const map = new YMap(
		document.getElementById('yandexMapSpbVoz'),
		{
			behaviors: ['drag', 'pinchZoom', 'mouseTilt', 'dblClick'],
			location: {
				center: [30.308628, 59.929353], // Центр СПб
				zoom: 14
			}
		}
	);

	map.addChild(new YMapDefaultSchemeLayer({ customization }));
	map.addChild(new YMapDefaultFeaturesLayer());

	map.addChild(new CustomMarkerWithPopup({
		coordinates: [30.308628, 59.929353],
		title: 'Свадебный салон Love Forever',
		address: 'Санкт-Петербург, м. Садовая, Вознесенский проспект, 18',
		phone: '8 812 425-67-82',
		workingHours: '10:00 - 22:00'
	}));
	map.addChild(new CustomMarkerWithPopup({
		coordinates: [30.317154, 59.927101],
		textMarker: true,
		title: 'м. САДОВАЯ'
	}));

		// Добавим маршрут (линия)
		const routeCoords = [
			[ 30.317154, 59.927101],
			[ 30.315797, 59.928504],
			[ 30.315459, 59.928896],
			[ 30.315505, 59.928966],
			[ 30.315224, 59.929083],
			[ 30.314595, 59.929748],
			[ 30.314638, 59.929766],
			[ 30.314519, 59.929871],
			[ 30.314473, 59.929867],
			[ 30.314448, 59.929887],
			[ 30.314274, 59.929842],
			[ 30.314281, 59.929809],
			[ 30.312342, 59.929290],
			[ 30.312309, 59.929302],
			[ 30.312161, 59.929269],
			[ 30.312114, 59.929223],
			[ 30.312114, 59.929223],
			[ 30.308360, 59.928272],
			[ 30.308405, 59.929382]
		];

		const polyline = new ymaps3.YMapFeature({
		geometry: {
			type: 'LineString',
			coordinates: routeCoords
		},
		style: {
			stroke: [{ color: '#F22EA9', width: 4 }]
		}
		});

		map.addChild(polyline);

	// map.addChild(new YMapDefaultSchemeLayer());
}

initMap()
	// 	ymaps3.ready.then(() => {
	// 	const { YMap, YMapDefaultSchemeLayer, YMapDefaultFeaturesLayer, YMapControls, YMapMarker } = ymaps3;

	// 	// Создаем карту
	// 	const map = new YMap(document.getElementById('yandexMapSpbVoz'), {
	// 		location: {
	// 			center: [30.3185, 59.9307], // Центр СПб
	// 			zoom: 14
	// 		}
	// 	});

	// 	// Добавляем тёмную тему
	// 	map.addChild(new YMapDefaultSchemeLayer({ theme: 'dark' }));
	// 	map.addChild(new YMapDefaultFeaturesLayer());

	// 	// Добавим маршрут (линия)
	// 	const routeCoords = [
	// 	[30.3122, 59.9357], // м. Адмиралтейская
	// 	[30.3136, 59.9334],
	// 	[30.3165, 59.9307],
	// 	[30.3227, 59.9274]  // м. Садовая
	// 	];

	// 	const polyline = new ymaps3.YMapFeature({
	// 	geometry: {
	// 		type: 'LineString',
	// 		coordinates: routeCoords
	// 	},
	// 	style: {
	// 		stroke: [{ color: '#f03bfc', width: 4 }]
	// 	}
	// 	});

	// 	map.addChild(polyline);

	// 	// Метка: Адмиралтейская
	// 	const admiralteyskaya = new YMapMarker({
	// 	coordinates: [30.3122, 59.9357],
	// 	title: 'м. Адмиралтейская'
	// 	});
	// 	admiralteyskaya.setContent(() => {
	// 	const el = document.createElement('div');
	// 	el.style = 'background:#f03bfc;color:white;padding:4px 6px;border-radius:8px;font-size:12px;';
	// 	el.textContent = 'м. Адмиралтейская';
	// 	return el;
	// 	});
	// 	map.addChild(admiralteyskaya);

	// 	// Метка: Садовая
	// 	const sadovaya = new YMapMarker({
	// 	coordinates: [30.3227, 59.9274],
	// 	title: 'м. Садовая'
	// 	});
	// 	sadovaya.setContent(() => {
	// 	const el = document.createElement('div');
	// 	el.style = 'background:#f03bfc;color:white;padding:4px 6px;border-radius:8px;font-size:12px;';
	// 	el.textContent = 'м. Садовая';
	// 	return el;
	// 	});
	// 	map.addChild(sadovaya);
	// });
// 		ymaps.ready(init);
// function init () {

// 	// Москва. Якиманка
// 	if ($('#yandexMapYa').length > 0) {

// 		var map = new ymaps.Map("yandexMapYa", {
// 				center: [55.730706,37.612023],
// 				zoom: 16
// 			}),
// 			balloon = new ymaps.Placemark([55.73218,37.611167], {
// 				balloonContentHeader: "Свадебный салон Love Forever",
// 				balloonContentBody: "Москва, ул. Большая Якиманка, 50<br /><br /><table><tr><td><img src='/images/icon-phone.png' alingn= width='14px' height='14px' /></td><td>&nbsp;<address class='ancillary_item ancillary_phone'><span class='phone_alloka'>8 812 425-69-36</span></address></td></tr><tr><td><img src='/images/icon-clock.png' width='14px' height='14px' /></td><td>&nbsp;10:00 - 22:00</td></tr></table>",
// 				balloonContentFooter: "<a href='/upload/yakimanka-route.pdf' target='_blank'>Посмотреть подробный маршрут</a>",
// 				hintContent: "Посмотреть контактные данные"
// 			}, {
// 				iconLayout: 'default#image',
// 				iconImageHref: '/images/place-marker.png',
// 				iconImageSize: [42, 44],
// 				iconImageOffset: [-14, -42]
// 			});

// 			var path1 = new ymaps.Polyline([
// 				[55.7293, 37.611565],
// 				[55.729625, 37.611522],
// 				[55.729885, 37.611768],
// 				[55.732361, 37.611849],
// 				[55.732346, 37.611355]
// 			], {
// 				balloonContent: "Путь от метро Октябрьская"
// 			}, {
// 				balloonCloseButton: false,
// 				strokeColor: "#f366c4",
// 				strokeWidth: 3
// 			});   
// 			var path2 = new ymaps.Polyline([
// 				[55.731234, 37.612509],
// 				[55.730834, 37.612433],
// 				[55.730610, 37.612433],
// 				[55.730622, 37.611854],
// 				[55.732368, 37.611865],
// 				[55.732371, 37.611253]
// 			], {
// 				balloonContent: "Путь от метро Октябрьская"
// 			}, {
// 				balloonCloseButton: false,
// 				strokeColor: "#b73bf1",
// 				strokeWidth: 3
// 			});   

// 		map.controls.add('zoomControl', { left: 5, top: 5 });
// 		map.geoObjects.add(balloon).add(path1).add(path2);
// 		map.behaviors.disable('scrollZoom');
// 		balloon.balloon.open();
// 		// map.setCenter('55.623535, 37.857816');
// 	}

// 	// Москва. Мастервкова
// 	if ($('#yandexMapAv').length > 0) {

// 		map = new ymaps.Map("yandexMapAv", {
// 				center: [55.709169, 37.658721],
// 				zoom: 17
// 			}),
// 			balloon = new ymaps.Placemark([55.709757, 37.659043], {
// 				balloonContentHeader: "Свадебный салон Love Forever",
// 				balloonContentBody: "Москва, ул. Мастеркова 1<br /><table><tr><td><img src='/images/icon-phone.png' alingn= width='14px' height='14px' /></td><td>&nbsp;<address class='ancillary_item ancillary_phone'><span class='phone_alloka'>8 812 425-69-36</span></address></td></tr><tr><td><img src='/images/icon-clock.png' width='14px' height='14px' /></td><td>&nbsp;10:00 - 22:00</td></tr></table>",
// 				balloonContentFooter: "",
// 				hintContent: "Посмотреть контактные данные"
// 			}, {
// 				iconLayout: 'default#image',
// 				iconImageHref: '/images/place-marker.png',
// 				iconImageSize: [42, 44],
// 				iconImageOffset: [-14, -42]            
// 			});

// 			var path = new ymaps.Polyline([
// 				[55.708527, 37.658142],
// 				[55.709375, 37.658270],
// 				[55.709533, 37.658667]
// 			], {
// 				balloonContent: "Путь от метро Автозаводская"
// 			}, {
// 				balloonCloseButton: false,
// 				strokeColor: "#f366c4",
// 				strokeWidth: 3
// 			});        

// 		map.controls.add('zoomControl', { left: 5, top: 5 });
// 		map.geoObjects.add(balloon).add(path);
// 		map.behaviors.disable('scrollZoom');
// 		balloon.balloon.open();
// 	}

// 	// Санкт-Петербург. Вознесенский проспект

// 			if ($('#yandexMapSpbVoz').length > 0) {

// 				var map = new ymaps.Map("yandexMapSpbVoz", {
// 						center: [59.929353, 30.308628],
// 						zoom: 15
// 					}),
// 					balloon = new ymaps.Placemark([59.929353, 30.308628], {
// 						balloonContentHeader: "Свадебный салон Love Forever",
// 						balloonContentBody: "Санкт-Петербург, м. Садовая, Вознесенский проспект, 18<br /><br /><table><tr><td><img src='/images/icon-phone.png' alingn= width='14px' height='14px' /></td><td>&nbsp;<address class='ancillary_item ancillary_phone'><span class='phone_alloka'>8 812 425-67-82</span></address></td></tr><tr><td><img src='/images/icon-clock.png' width='14px' height='14px' /></td><td>&nbsp;10:00 - 22:00</td></tr></table>",
// 						balloonContentFooter: "",
// 						hintContent: "Посмотреть контактные данные"
// 					}, {
// 						iconLayout: 'default#image',
// 						iconImageHref: '<?php echo esc_url( get_template_directory_uri() . '/images/map-marker.svg' ); ?>',
// 						iconImageSize: [26.83, 46.39],
// 						iconImageOffset: [-14, -42]         
// 					});

// 					var path = new ymaps.Polyline([
// 						[59.927101, 30.317154],
// 						[59.928504, 30.315797],
// 						[59.928896, 30.315459],
// 						[59.928966, 30.315505],
// 						[59.929083, 30.315224],
// 						[59.929748, 30.314595],
// 						[59.929766, 30.314638],
// 						[59.929871, 30.314519],
// 						[59.929867, 30.314473],
// 						[59.929887, 30.314448],
// 						[59.929842, 30.314274],
// 						[59.929809, 30.314281],
// 						[59.929290, 30.312342],
// 						[59.929302, 30.312309],
// 						[59.929269, 30.312161],
// 						[59.929223, 30.312114],
// 						[59.929223, 30.312114],
// 						[59.928272, 30.308360],
// 						[59.929382, 30.308405]
// 					], {
// 						balloonContent: "Путь от метро Садовая"
// 					}, {
// 						balloonCloseButton: false,
// 						strokeColor: "#f366c4",
// 						strokeWidth: 3
// 					});

// 				map.controls.add('zoomControl', { left: 5, top: 5 });
// 				map.geoObjects.add(balloon).add(path);
// 				map.behaviors.disable('scrollZoom');
// 				// map.setType('yandex#dark');
// 				balloon.balloon.open();
// 			}






// 	/*
// 		var map = new ymaps.Map("yandexMapKr", {
// 				center: [55.732984, 37.666569],
// 				zoom: 17
// 			}),
// 			balloon = new ymaps.Placemark([55.733647, 37.666569], {
// 				balloonContentHeader: "Свадебный салон Love Forever",
// 				balloonContentBody: "<br />Москва, ул. Марксистская, 38<br /><table><tr><td><img src='/images/icon-phone.png' alingn= width='14px' height='14px' /></td><td>&nbsp;<address class='ancillary_item ancillary_phone'><span class='phone_alloka'>8 812 425-69-36</span></address></td></tr><tr><td><img src='/images/icon-clock.png' width='14px' height='14px' /></td><td>&nbsp;10:00 - 22:00</td></tr></table>",
// 				balloonContentFooter: "",
// 				hintContent: "Посмотреть контактные данные"
// 			}, {
// 				iconLayout: 'default#image',
// 				iconImageHref: '/images/place-marker.png',
// 				iconImageSize: [42, 44],
// 				iconImageOffset: [-14, -42]            
// 			});

// 			var path = new ymaps.Polyline([
// 				[55.732252, 37.665657],
// 				[55.732948, 37.666944],
// 				[55.733287, 37.666483],
// 				[55.733590, 37.667062]
// 			], {
// 				balloonContent: "Путь от метро Крестьянская Застава"
// 			}, {
// 				balloonCloseButton: false,
// 				strokeColor: "#f366c4",
// 				strokeWidth: 3
// 			});        

// 		map.controls.add('zoomControl', { left: 5, top: 5 });
// 		map.geoObjects.add(balloon).add(path);
// 		balloon.balloon.open();
// 	*/

// 	/*
// 		var map = new ymaps.Map("yandexMapTs", {
// 				center: [55.61875,37.506266],
// 				zoom: 17
// 			}),
// 			balloon = new ymaps.Placemark([55.61813,37.507484], {
// 				balloonContentHeader: "Свадебный салон Love Forever",
// 				balloonContentBody: "<br />Москва, ул. Профсоюзная, 129а<br />ТРК «ПРИНЦ ПЛАЗА» (4 этаж)<br /><table><tr><td><img src='/images/icon-phone.png' alingn= width='14px' height='14px' /></td><td>&nbsp;<address class='ancillary_item ancillary_phone'><span class='phone_alloka'>8 812 425-69-36</span></address></td></tr><tr><td><img src='/images/icon-clock.png' width='14px' height='14px' /></td><td>&nbsp;10:00 - 22:00</td></tr></table>",
// 				balloonContentFooter: "<a href='/upload/ts-route.pdf' target='_blank'>Посмотреть подробный маршрут</a>",
// 				hintContent: "Посмотреть контактные данные"
// 			}, {
// 				iconLayout: 'default#image',
// 				iconImageHref: '/images/place-marker.png',
// 				iconImageSize: [42, 44],
// 				iconImageOffset: [-14, -42]            
// 			});

// 		map.controls.add('zoomControl', { left: 5, top: 5 });
// 		map.geoObjects.add(balloon);
// 		balloon.balloon.open();
// 	*/
// }
	</script>
<?php endif; ?>
