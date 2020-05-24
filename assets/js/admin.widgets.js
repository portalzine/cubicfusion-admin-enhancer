/*! 
	cubicFUSION Admin Enhancer \ Dashboard Gutenberg
	Alex @ portalZINE NMN
	https://portalzine.de/cubicfusion

*/
const {registerBlockType} = wp.blocks; 
const {createElement} = wp.element; 
const {__} = wp.i18n; 
const { PanelColorSettings,InspectorControls} = wp.editor; 
const {TextControl,SelectControl,ServerSideRender, Panel, PanelBody, PanelRow} = wp.components; //WordPress form inputs and server-side renderer

registerBlockType( 'cf-blocks/admin-widgets', {
	title: __( 'Dashboard Widgets' ), 
	category:  __( 'cf-blocks' ), 
	attributes:  {		
		shortcode: {
			default: ''
		},
		color: {
			default: ''
		},
		textColor: {
			default: ''
		},
		linkColor: {
			default: ''
		},
		className: {
			default: ''
		}
		
	},
	edit(props){
		const attributes =  props.attributes;
		const setAttributes =  props.setAttributes;

	
		function changeShortcode(shortcode){
			setAttributes({shortcode});
		}
	
		return createElement('div', {}, [
		
			createElement( ServerSideRender, {
				block: 'cf-blocks/admin-widgets',
				attributes: attributes
			} ),
			
			createElement( InspectorControls, {},
				[
				createElement(PanelBody, { title: 'Content', initialOpen: true }, [	
				
					createElement(SelectControl, {
						value: attributes.shortcode,
						label: __( 'Shortcode' ),
						onChange: changeShortcode,
						options: CF.my_options
					})
				]),
				createElement(PanelColorSettings, {
                  title: __('Override Color Settings'),
                  colorSettings: [{
                    value: attributes.color,
                    onChange: function onChange(colorValue) {
                      return setAttributes({
                        color: colorValue
                      });
                    },
                    label: __('Background Color')
                  }, {
                    value: attributes.textColor,
                    onChange: function onChange(colorValue) {
                      return setAttributes({
                        textColor: colorValue
                      });
                    },
                    label: __('Text Color')
                  },
                                  {
                    value: attributes.linkColor,
                    onChange: function onChange(colorValue) {
                      return setAttributes({
                        linkColor: colorValue
                      });
                    },
                    label: __('Link Color')
                  }]
                })			
			]
			)
		] )
	},
	save(){
		return null;
	}
});