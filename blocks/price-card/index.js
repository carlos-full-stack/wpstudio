( function() {
	const { __ } = wp.i18n;
	const { registerBlockType } = wp.blocks;
	const { 
		useBlockProps, 
		InspectorControls,
		RichText,
		MediaUploadCheck,
		MediaUpload,
		BlockControls
	} = wp.blockEditor;
	const { 
		PanelBody, 
		Button, 
		TextControl,
		ToggleControl,
		Toolbar
	} = wp.components;
	const { createElement: el } = wp.element;

	registerBlockType( 'wpstudio/price-card', {
		title: __( 'Price Card', 'wpstudio' ),
		description: __( 'Card de precio con características, flexible para cualquier página', 'wpstudio' ),
		category: 'common',
		icon: 'tag',
		keywords: [ 'price', 'card', 'pricing', 'servicios' ],
		supports: {
			html: false,
			align: false
		},
		attributes: {
			title: {
				type: 'string',
				default: 'Nombre del servicio'
			},
			price: {
				type: 'string',
				default: '599'
			},
			icon: {
				type: 'string',
				default: ''
			},
			badge: {
				type: 'boolean',
				default: false
			},
			features: {
				type: 'array',
				default: [
					{
						titulo: 'Característica 1',
						descripcion: ''
					}
				]
			},
			buttonUrl: {
				type: 'string',
				default: '#contacto'
			}
		},

		edit: function( props ) {
			const { attributes, setAttributes } = props;
			const { title, price, icon, badge, features, buttonUrl } = attributes;
			const blockProps = useBlockProps();

			const updateFeature = function( index, field, value ) {
				const newFeatures = [ ...features ];
				newFeatures[ index ][ field ] = value;
				setAttributes( { features: newFeatures } );
			};

			const addFeature = function() {
				setAttributes( { 
					features: [ 
						...features, 
						{ titulo: '', descripcion: '' } 
					] 
				} );
			};

			const removeFeature = function( index ) {
				setAttributes( { 
					features: features.filter( function( _, i ) { return i !== index; } )
				} );
			};

			return el(
				wp.element.Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Información de la Card', 'wpstudio' ), initialOpen: true },
						el(
							TextControl,
							{
								label: __( 'Título', 'wpstudio' ),
								value: title,
								onChange: function( value ) { setAttributes( { title: value } ); }
							}
						),
						el(
							TextControl,
							{
								label: __( 'Precio (sin €)', 'wpstudio' ),
								value: price,
								onChange: function( value ) { setAttributes( { price: value } ); },
								type: 'number'
							}
						),
						el(
							'div',
							null,
							el(
								'label',
								null,
								__( 'Icono (SVG o URL)', 'wpstudio' )
							),
							el(
								'textarea',
								{
									value: icon,
									onChange: function( e ) { setAttributes( { icon: e.target.value } ); },
									placeholder: __( 'Pega aquí el código SVG o la URL de la imagen', 'wpstudio' ),
									style: { width: '100%', minHeight: '100px', fontFamily: 'monospace', fontSize: '12px', marginTop: '8px' }
								}
							)
						),
						el(
							ToggleControl,
							{
								label: __( 'Marcar como "Más Popular"', 'wpstudio' ),
								checked: badge,
								onChange: function( value ) { setAttributes( { badge: value } ); }
							}
						),
						el(
							TextControl,
							{
								label: __( 'URL del botón', 'wpstudio' ),
								value: buttonUrl,
								onChange: function( value ) { setAttributes( { buttonUrl: value } ); },
								type: 'url'
							}
						)
					),
					el(
						PanelBody,
						{ title: __( 'Características', 'wpstudio' ), initialOpen: true },
						features.map( function( feature, index ) {
							return el(
								'div',
								{ key: index, style: { marginBottom: '20px', paddingBottom: '20px', borderBottom: '1px solid #ccc' } },
								el(
									TextControl,
									{
										label: __( 'Título de característica', 'wpstudio' ),
										value: feature.titulo,
										onChange: function( value ) { updateFeature( index, 'titulo', value ); },
										placeholder: __( 'Ej: Incluye hosting', 'wpstudio' )
									}
								),
								el(
									TextControl,
									{
										label: __( 'Descripción (opcional)', 'wpstudio' ),
										value: feature.descripcion,
										onChange: function( value ) { updateFeature( index, 'descripcion', value ); },
										placeholder: __( 'Descripción breve', 'wpstudio' ),
										help: __( 'Aparecerá debajo del título', 'wpstudio' )
									}
								),
								el(
									Button,
									{
										isDestructive: true,
										onClick: function() { removeFeature( index ); }
									},
									__( 'Eliminar característica', 'wpstudio' )
								)
							);
						} ),
						el(
							Button,
							{
								isPrimary: true,
								onClick: addFeature
							},
							__( 'Añadir característica', 'wpstudio' )
						)
					)
				),
				el(
					'div',
					blockProps,
					el(
						'div',
						{ className: 'wp-block-group is-style-price-card is-style-border-accent' },
						icon && el(
							'div',
							{ className: 'price-card-icon' },
							icon.includes( '<svg' ) ? 
								el( 'span', { dangerouslySetInnerHTML: { __html: icon } } ) :
								el( 'img', { src: icon, alt: '', width: '40', height: '40', loading: 'lazy' } )
						),
						el(
							'div',
							{ className: 'price-card-header' },
							el( 'h4', { className: 'price-card-title' }, title ),
							badge && el( 'span', { className: 'price-card-badge' }, __( 'Más Popular', 'wpstudio' ) )
						),
						el(
							'p',
							{ className: 'price-card-price has-max-48-font-size' },
							price + '€ ',
							el( 'span', { className: 'price-card-iva' }, __( '+ IVA', 'wpstudio' ) )
						),
						el( 'hr', { className: 'price-card-divider' } ),
						features && features.length > 0 && el(
							'ul',
							{ className: 'wp-block-list is-style-check-list' },
							features.map( function( feature, index ) {
								return el(
									'li',
									{ key: index },
									el( 'span', { className: 'price-card-feature-title' }, feature.titulo ),
									feature.descripcion && el(
										'span',
										{ className: 'price-card-feature-description' },
										feature.descripcion
									)
								);
							} )
						),
						el(
							'div',
							{ className: 'wp-block-buttons' },
							el(
								'div',
								{ className: 'wp-block-button is-style-icon-arrow' },
								el(
									'a',
									{ className: 'wp-block-button__link wp-element-button', href: buttonUrl },
									__( 'Me interesa', 'wpstudio' )
								)
							)
						)
					)
				)
			);
		},

		save: function( props ) {
			const { attributes } = props;
			const { title, price, icon, badge, features, buttonUrl } = attributes;

			return el(
				'div',
				useBlockProps.save(),
				el(
					'div',
					{ className: 'wp-block-group is-style-price-card is-style-border-accent' },
					icon && el(
						'div',
						{ className: 'price-card-icon' },
						icon.includes( '<svg' ) ? 
							el( 'span', { dangerouslySetInnerHTML: { __html: icon } } ) :
							el( 'img', { src: icon, alt: '', width: '40', height: '40', loading: 'lazy' } )
					),
					el(
						'div',
						{ className: 'price-card-header' },
						el( 'h4', { className: 'price-card-title' }, title ),
						badge && el( 'span', { className: 'price-card-badge' }, __( 'Más Popular', 'wpstudio' ) )
					),
					el(
						'p',
						{ className: 'price-card-price has-max-48-font-size' },
						price + '€ ',
						el( 'span', { className: 'price-card-iva' }, __( '+ IVA', 'wpstudio' ) )
					),
					el( 'hr', { className: 'price-card-divider' } ),
					features && features.length > 0 && el(
						'ul',
						{ className: 'wp-block-list is-style-check-list' },
						features.map( function( feature, index ) {
							return el(
								'li',
								{ key: index },
								el( 'span', { className: 'price-card-feature-title' }, feature.titulo ),
								feature.descripcion && el(
									'span',
									{ className: 'price-card-feature-description' },
									feature.descripcion
								)
							);
						} )
					),
					el(
						'div',
						{ className: 'wp-block-buttons' },
						el(
							'div',
							{ className: 'wp-block-button is-style-icon-arrow' },
							el(
								'a',
								{ className: 'wp-block-button__link wp-element-button', href: buttonUrl },
								__( 'Me interesa', 'wpstudio' )
							)
						)
					)
				)
			);
		}
	} );
} )();
