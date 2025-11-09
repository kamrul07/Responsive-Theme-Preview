(function(wp){
	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment } = wp.element;
	const { InspectorControls, MediaUpload } = (wp.blockEditor || wp.editor);
	const { PanelBody, RangeControl, SelectControl, TextControl, ColorPalette, Button } = wp.components;
	const __ = wp.i18n.__;

	const defaultBreakpoints = [
		{ title: 'Mobile', width: 375, icon: '' },
		{ title: 'Tablet', width: 768, icon: '' },
		{ title: 'Desktop', width: 1280, icon: '' }
	];

	registerBlockType('rtp/responsive-preview', {
		title: __('Responsive Preview','responsive-theme-preview'),
		icon: 'visibility',
		category: 'widgets',
		attributes: {
			columns: { type:'number', default:3 },
			source:  { type:'string', default:'static' },
			count:   { type:'number', default:6 },
			itemsArr:{ type:'array', default: [] },
			items:   { type:'string', default:'' },
			breakpoints: { type:'array', default: defaultBreakpoints },
			ctaText: { type:'string', default:'Open Live' },
			ctaLink: { type:'string', default:'' },
			overlayBg: { type:'string', default:'rgba(0,0,0,.6)' },
			topbarBg: { type:'string', default:'#0f172a' },
			previewBtnPos: { type:'string', default:'pos-br' },
			previewType: { type:'string', default:'popup' },
		},
		edit: function(props){
			const a = props.attributes;
			const set = props.setAttributes;

			function updateBreakpoint(idx, key, value){
				const arr = (a.breakpoints || []).slice();
				arr[idx] = Object.assign({}, arr[idx], { [key]: value });
				set({ breakpoints: arr });
			}
			function addBreakpoint(){ const arr=(a.breakpoints||[]).slice(); arr.push({ title:'Custom', width:1024, icon:''}); set({ breakpoints: arr }); }
			function removeBreakpoint(idx){ const arr=(a.breakpoints||[]).slice(); arr.splice(idx,1); set({ breakpoints: arr }); }

			function updateItem(idx, key, value){
				const arr = (a.itemsArr || []).slice();
				arr[idx] = Object.assign({}, arr[idx] || {}, { [key]: value });
				set({ itemsArr: arr });
			}
			function addItem(){ const arr=(a.itemsArr||[]).slice(); arr.push({ title:'', image:'', url:'', btn: __('Preview','responsive-theme-preview') }); set({ itemsArr: arr }); }
			function removeItem(idx){ const arr=(a.itemsArr||[]).slice(); arr.splice(idx,1); set({ itemsArr: arr }); }

			return el(Fragment, null,
				el(InspectorControls, null,
					el(PanelBody, { title: __('Source','responsive-theme-preview'), initialOpen: true },
						el(SelectControl, { label: __('Source','responsive-theme-preview'), value: a.source, options: [
							{ label: __('Static (Repeater)','responsive-theme-preview'), value: 'static' },
							{ label: __('Dynamic (CPT: Previews)','responsive-theme-preview'), value: 'dynamic' }
						], onChange: (v)=> set({ source: v }) }),
						(a.source==='dynamic') && el(RangeControl, { label: __('Items (dynamic)','responsive-theme-preview'), value: a.count, min:1, max:48, onChange: (v)=> set({ count: v }) }),
						(a.source==='dynamic') && el(SelectControl, { label: __('Preview Type','responsive-theme-preview'), value: a.previewType, options: [
							{ label: __('Popup','responsive-theme-preview'), value: 'popup' },
							{ label: __('Separate URL','responsive-theme-preview'), value: 'page' }
						], onChange: (v)=> set({ previewType: v }) }),
						(a.source==='static') &&
							el('div', {},
								(a.itemsArr || []).map(function(item, idx){
									return el('div', { key: idx, style:{border:'1px solid #ddd', padding:'10px', marginBottom:'10px'} },
										el(TextControl, { label: __('Title','responsive-theme-preview'), value: item.title || '', onChange: v=>updateItem(idx,'title',v) }),
										el('div', {},
											el('label', {}, __('Image','responsive-theme-preview')),
											el(MediaUpload, {
												onSelect: (media)=> updateItem(idx,'image', (media && media.url) ? media.url : '' ),
												allowedTypes: ['image'],
												render: function({open}){
													return el('div', {},
														item.image ? el('img', { src:item.image, style:{maxWidth:'100%', height:'auto', display:'block', marginBottom:'6px'} }) : null,
														el(Button, { onClick: open, isSecondary: true }, item.image ? __('Replace','responsive-theme-preview') : __('Upload','responsive-theme-preview'))
													);
												}
											})
										),
										el(TextControl, { label: __('Preview URL','responsive-theme-preview'), value: item.url || '', onChange: v=>updateItem(idx,'url',v) }),
										el(TextControl, { label: __('Button Text','responsive-theme-preview'), value: item.btn || '', onChange: v=>updateItem(idx,'btn',v) }),
										el(Button, { isDestructive:true, onClick: ()=>removeItem(idx), style:{marginTop:'8px'} }, __('Remove Item','responsive-theme-preview'))
									);
								}),
								el(Button, { isSecondary:true, onClick: addItem }, __('Add Item','responsive-theme-preview'))
							)
					),
					el(PanelBody, { title: __('Breakpoints','responsive-theme-preview'), initialOpen: false },
						el('div', { },
							(a.breakpoints || []).map(function(bp, idx){
								return el('div', { key: idx, style:{border:'1px solid #ddd', padding:'8px', marginBottom:'8px'} },
									el(TextControl, { label: __('Title','responsive-theme-preview'), value: bp.title || '', onChange: v=>updateBreakpoint(idx,'title',v) }),
									el(TextControl, { label: __('Icon Image URL','responsive-theme-preview'), value: bp.icon || '', onChange: v=>updateBreakpoint(idx,'icon',v) }),
									el(RangeControl, { label: __('Width','responsive-theme-preview'), value: bp.width || 1280, min:240, max:1920, onChange: v=>updateBreakpoint(idx,'width',v) }),
									el(Button, { isDestructive: true, onClick: ()=>removeBreakpoint(idx) }, __('Remove','responsive-theme-preview'))
								);
							}),
							el(Button, { isSecondary: true, onClick: addBreakpoint }, __('Add Breakpoint','responsive-theme-preview'))
						)
					),
					el(PanelBody, { title: __('CTA & Styles','responsive-theme-preview'), initialOpen: false },
						el(TextControl, { label: __('CTA Text','responsive-theme-preview'), value: a.ctaText, onChange: v=>set({ ctaText: v }) }),
						el(TextControl, { label: __('CTA Link','responsive-theme-preview'), value: a.ctaLink, onChange: v=>set({ ctaLink: v }) }),
						el(SelectControl, { label: __('Preview Button Position','responsive-theme-preview'), value: a.previewBtnPos, options: [
							{ label: __('Bottom Right','responsive-theme-preview'), value: 'pos-br' },
							{ label: __('Bottom Left','responsive-theme-preview'), value: 'pos-bl' },
							{ label: __('Top Right','responsive-theme-preview'), value: 'pos-tr' },
							{ label: __('Top Left','responsive-theme-preview'), value: 'pos-tl' }
						], onChange: v=>set({ previewBtnPos: v }) }),
						el('div', { style:{ marginTop:'8px' } },
							el('label', null, __('Overlay Background','responsive-theme-preview')),
							el(ColorPalette, { value: a.overlayBg, onChange: v=>set({ overlayBg: v }) })
						),
						el('div', { style:{ marginTop:'8px' } },
							el('label', null, __('Topbar Background','responsive-theme-preview')),
							el(ColorPalette, { value: a.topbarBg, onChange: v=>set({ topbarBg: v }) })
						)
					)
				),
				el('div', { className:'rtp-placeholder'}, __('Responsive Preview – renders on the front-end.','responsive-theme-preview'))
			);
		},
		save: function(){ return null; }
	});
})(window.wp);