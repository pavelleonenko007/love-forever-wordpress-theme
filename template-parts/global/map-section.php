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
          this.isOpened = false;

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
            !this.isOpened ? 
				this._openPopup() : this._closePopup();
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

          this.isOpened = true;

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

          this.isOpened = false;

			this.removeChild(this._popup);
			this._popup = null;
			this._marker?.element?.classList.remove('marker--selected');
		}
	}

	const map = new YMap(
		document.getElementById('yandexMapSpbVoz'),
		{
			behaviors: ['drag', 'pinchZoom', 'mouseTilt'],
			location: {
				center: [30.308628, 59.929353], // Центр СПб
				zoom: 15
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
	map.addChild(new CustomMarkerWithPopup({
		coordinates: [30.31509599999979,59.935973615955675],
		textMarker: true,
		title: 'м. АДМИРАЛТЕЙСКАЯ'
	}));

		// Добавим маршрут (линия)
		const sadovayaRouteCoords = [
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

		const admiralteyskayaRouteCoords = [[30.31509599999979,59.935973615955675],[30.316297629638417,59.93528447101205],[30.316115239425418,59.935182174838815],[30.316340544982655,59.93508526238268],[30.3098817856748,59.932764662604704],[30.30913760272961,59.93258836839017],[30.309513111991688,59.93212530331308],[30.309448738975345,59.93186146100795],[30.310049553794684,59.93156530897351],[30.310264130515865,59.931339154724334],[30.308365126533324,59.93097299694558],[30.308374156158166,59.93041274388374]];

		const sadovayaRoute = new ymaps3.YMapFeature({
		geometry: {
			type: 'LineString',
			coordinates: sadovayaRouteCoords
		},
		style: {
			stroke: [{ color: '#F22EA9', width: 4 }]
		}
		});

		const admiralteyskayaRoute = new ymaps3.YMapFeature({
		geometry: {
			type: 'LineString',
			coordinates: admiralteyskayaRouteCoords
		},
		style: {
			stroke: [{ color: '#F22EA9', width: 4 }]
		}
		});

		map.addChild(sadovayaRoute);
		map.addChild(admiralteyskayaRoute);
}

initMap();
	</script>
<?php endif; ?>
