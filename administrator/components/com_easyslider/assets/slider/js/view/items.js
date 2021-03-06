void function ( exports, $, _, Backbone ) {

	var ES_ItemView = exports.ES_ItemView = B.View.extend({
		constructor: function ES_ItemView () {
			this.previewAnimIn = _.debounce(this.previewAnimIn, 100)
			this.previewAnimOut = _.debounce(this.previewAnimOut, 100)
			B.View.apply(this, arguments);
		},
		events: {
			'mousedown .item-content': 'stopPropagation',
			'dblclick .item-content *': 'editContent',
			'change .item-content': 'stopPropagation',
			'focus .item-content': 'editContent',
			'blur .item-content': 'saveContent',
		},
		modelEvents: {
			'remove': 'remove',
			'change:style.font.family': 'loadFont',
			'change:animation.in': 'previewAnimIn',
			'change:animation.out': 'previewAnimOut',
		},
		bindings: [
			{
				type: 'class',
				attr: {
					'locked': 'locked',
					'hidden': 'hidden',
					'selected': 'selected',
				}
			},
			{
				type: 'style',
				attr: {
					'visibility': 'style.visible',
					'zIndex': 'index',
					'left': 'style.position.x',
					'top': 'style.position.y',
				},
				parse: function ( value, key ) {
					switch ( key ) {
						case 'visibility' :
							return value ? 'visible' : 'hidden';
						case 'left':
						case 'top':
							return value * 100 + '%';
						default :
							return value;
					}
				}
			},
			{
				selector: '.item-offset',
				type: 'style',
				attr: {
					'left': 'style.offset.x',
					'top': 'style.offset.y',
					'width': 'style.width',
					'height': 'style.height',
				},
			},
			{
				selector: '.item-container',
				type: 'style',
				attr: {
					'backgroundColor': 'style.background.color',
					'backgroundImage': 'style.background.image.src',
					'backgroundPosition': 'style.background.position',
					'backgroundRepeat': 'style.background.repeat',
					'backgroundSize': 'style.background.size',
					'borderWidth': 'style.border.width',
					'borderStyle': 'style.border.style',
					'borderColor': 'style.border.color',
                    'opacity': 'style.opacity',
					'borderRadius': 'style.border.radius',
					'boxShadow': 'style.box_shadows',
				},
				parse: function ( value, key, view ) {
					if( value ) {
						switch ( key ) {
							case 'backgroundImage':
								//if ( value == ' ') {
								//	console.log('zxczxc')
								//	view.$('.item-container').get(0).style.backgroundImage = ' ';
								//}
								return value && value !== ' ' ? 'url(' + ES_App.getImageURL(value) + ')' : 'url( )';
							case 'boxShadow':
								var html = '';
								if (value) {
									_(value).each(function(shadow, i){
                                        html += shadow.x + 'px ' + shadow.y + 'px ' + shadow.blur + 'px ' + shadow.color + (shadow.inset ? ' inset' : '') + (i == value.length - 1 ? '' : ', ') ;
                                    })
                                }
                                return html;
							default:
								return value;
						}
					}
				}
			},
			{
				selector: '.item-content',
				type: 'style',
				attr: {
					'color': 'style.font.color',
					'fontFamily': 'style.font.family',
					'fontSize': 'style.font.size',
					'fontWeight': 'style.font.weight',
					'fontStyle': 'style.font.style',
					'lineHeight': 'style.line_height',
					'letterSpacing': 'style.letter_spacing',
					'textDecoration': 'style.text_decoration',
					'paddingTop': 'style.padding.top',
					'paddingLeft': 'style.padding.left',
					'paddingRight': 'style.padding.right',
					'paddingBottom': 'style.padding.bottom',

					'textAlign': 'style.align_h',
                    'textShadow': 'style.text_shadows',

					'alignItems': 'style.flex.alignItems',
					'alignContent': 'style.flex.alignContent',
					'justifyContent': 'style.flex.justifyContent',
					'flexDirection': 'style.flex.direction',
					'flexWrap': 'style.flex.wrap',
					'flexBasis': 'style.flex.basis',
					'flexGrow': 'style.flex.grow',

				},
				parse: function ( value, key ) {
					switch ( key ) {
						case 'fontSize':
							return value + 'px';
						case 'flexBasis':
							return value + 'px';
						case 'flexGrow':
							return value ? '1' : '0';
                        case 'textShadow':
                            var html = '';
                            if (value) {
                                _(value).each(function(shadow, i){
                                    html += shadow.x + 'px ' + shadow.y + 'px ' + shadow.blur + 'px ' + shadow.color + (i == value.length - 1 ? '' : ', ') ;
                                })
                            }
                            return html;
						default :
							return value;
					}
				}
			}
		],

		initialize: function () {
			this.model.itemView = this;
		},
		ready: function () {
			this.sliderView = this.superView.superView;
			this.selectionsView = this.sliderView.selectionsView;
			this.$offset = this.$('.item-offset');
			this.$content = this.$('.item-content');
			this.$container = this.$('.item-container');
			this.$animation = this.$('.item-animation');

			this.medium = new Medium({
				element: this.$content.get(0),
				mode: Medium.richMode,
				autofocus: false,
				autoHR: false,
				maxLength: -1,
				modifiers: {
					'b': 'bold',
					'i': 'italicize',
					'u': 'underline',
					'v': 'paste'
				},
				cssClasses: {
					editor: 'Medium',
					pasteHook: 'Medium-paste-hook',
					placeholder: 'Medium-placeholder',
					clear: 'Medium-clear'
				},
				attributes: null,
				pasteAsText: false,
				tags: {
					'paragraph': 'div',
					'outerLevel': [ 'a', 'div', 'li' ],
					'innerLevel': [ 'b', 'u', 'i', 'img', 'strong', 'span', 'sup', 'sub' ]
				}
			});

			this.animation_in = ES_Timeline({ align: 'normal' })
			this.animation_out = ES_Timeline({ align: 'normal ' })
			this.animation = ES_Timeline({ align: 'normal' })
				.add(this.animation_out)
				.add(this.animation_in)

			this.listenTo(this.animation, 'end', this.exitPreview)
			this.listenTo(this.animation_in, 'end', this.exitPreview)
			this.listenTo(this.animation_out, 'end', this.exitPreview)

			this.loadFont()
		},
		editContent: function ( e ) {
			if ( this.model.get('type') !== 'text' || this._isEditing )
				return;
			this.rootView.$el.addClass('text-item-edit-mode');
			this.model.selectionView.$el.addClass('edit-mode')
			this.$el.addClass('edit-mode')
			this.rootView.mediumToolbar.inspect(this.medium, e)
			this.rootView.itemInspector.close()
			this.$el.siblings('.edit-mode').find('.item-content').trigger('blur')
			this.medium.focus()
			this._isEditing = true;
		},
		saveContent: function ( e ) {
			this.$content.trigger('changecontent');
			if ( !this.$content.hasClass('medium-toolbar-editing') ) {
				this.rootView.$el.removeClass('text-item-edit-mode');
				this.model.selectionView.$el.removeClass('edit-mode')
				this.$el.removeClass('edit-mode')
				this._isEditing = false;
				this.rootView.mediumToolbar.release()
			}
			//this.saveSize();
		},
		loadFont: function () {
			this.rootView.fontsLoader.load(this.model.get('style.font.family'));
		},
		enterPreview: function () {

			/* -- IMPORTANT -- */
			// This function is identical to "item.ready" function in "easyslider.js"
			// This function is used to define in out animation for items

			this.$container.removeClass('hidden')
			this.$animation.html(this.$container.get(0).outerHTML)
			this.$container.addClass('hidden')
			this.$animation.removeClass('hidden')

			var sliderType = parseInt(this.model.root.get('layout.type'));
			var timelineMode = parseInt(this.model.root.get('timeline.mode'));

			this.__dataBinding.updateView();

			this.$animation = this.$('.item-animation').css('opacity', 1);

			switch ( parseInt(this.model.get('animation.in.split')) ) {
				case 1:
					this.$inElements = this.$animation.find('.item-content > *');
					break;
				case 2:
					this.$inElements = this.$animation.find('.split-word');
					break;
				case 3:
					this.$inElements = this.$animation.find('.split-char');
					break;
				default:
					this.$inElements = this.$animation;
			}
			switch ( parseInt(this.model.get('animation.out.split')) ) {
				case 1:
					this.$outElements = this.$animation.find('.item-content > *');
					break;
				case 2:
					this.$outElements = this.$animation.find('.split-word');
					break;
				case 3:
					this.$outElements = this.$animation.find('.split-char');
					break;
				default:
					this.$outElements = this.$animation;
			}

			var tweenIn = this.model.get('animation.in').getTweenObj();
			var tweenOut = this.model.get('animation.out').getTweenObj();

			this.animation_out = ES_Timeline.staggerTo(this.$outElements, this.model.get('animation.out.splitDelay'), tweenOut, { paused: true });

			this.animation_in = ES_Timeline.staggerFrom(this.$inElements, this.model.get('animation.in.splitDelay'), tweenIn, { paused: true });

			this.animation = ES_Timeline({ align: 'normal', paused: true })
					.add(this.animation_in)
					.add(this.animation_out);


			//delete this.animation;

			//switch (sliderType) {
			//	case 2:
			//		//if (timelineMode == 1)
			//		tweenIn.delay = 0;
			//		tweenOut.delay = 0;
			//		break;
			//}
			//this.animation_in.tweens = ES_Timeline({ align: 'normal' })
			//		.staggerFrom(this.$inElements, this.model.get('animation.in.splitDelay'), tweenIn).tweens;
			//
			//this.animation_out.tweens = ES_Timeline({ align: 'normal ' })
			//		.staggerTo(this.$outElements, this.model.get('animation.out.splitDelay'), tweenOut).tweens;
			//
			//if (parseInt(this.model.get('animation.in.split')) == 1) {
			//	this.animation_in.tweens = ES_Timeline({ align: 'normal' })
			//		.staggerFrom(this.$inElements, this.model.get('animation.in.splitDelay'), tweenIn).tweens;
			//}
			//else {
			//	this.animation_in.tweens = ES_Tween(this.$inElements, _.pick(tweenIn, 'duration', 'delay')).tweens;
			//}
			//if (parseInt(this.model.get('animation.out.split')) == 1) {
			//	this.animation_out.tweens = ES_Timeline({ align: 'normal ' })
			//		.staggerTo(this.$outElements, this.model.get('animation.out.splitDelay'), tweenOut).tweens;
			//}
			//else {
			//	this.animation_out.tweens = ES_Tween(this.$outElements, _.pick(tweenOut, 'duration', 'delay')).tweens;
			//}
			//
			//this.animation_in.$el = this.$inElements;
			//this.animation_out.$el = this.$outElements;
			//
			//this.animation.duration = 0;
			//this.animation.tweens = [];
			//this.animation
			//	.add(this.animation_in)
			//	.add(this.animation_out);
			//this.animation.refreshDuration();
			//
			//this.rootView.slideAnim._render(this.rootView.slideAnim.position());
		},
		enterPreview2: function() {
			this.$container.removeClass('hidden')
			this.$animation.html(this.$container.get(0).outerHTML)
			this.$container.addClass('hidden')
			this.$animation.removeClass('hidden')
			//new ES_SplitText(this.$animation.find('.item-content > *'))

			switch ( this.model.get('animation.in.split') ) {
				case "1":
					var elements = this.$animation.find('.item-content > *');
					break;
				case "2":
					var elements = this.$animation.find('.split-word');
					break;
				case "3":
					var elements = this.$animation.find('.split-char');
					break;
				default:
					var elements = this.$animation;
			}

			this.animation_out.tweens = [];
			//this.animation_out.delay = this.model.get('animation.out.delay');
			//this.animation_out.duration = 0;
			//this.animation_out.tweens = ES_Timeline.staggerTo(elements, this.model.get('animation.out.splitDelay'), this.model.get('animation.out').getTweenObj()).tweens;
			//this.animation_out.refreshDuration()

			this.animation_in.tweens = [];
			this.animation_in.delay = this.model.get('animation.in.delay');
			this.animation_in.duration = 0;
			this.animation_in.tweens = ES_Timeline.staggerFrom(elements, this.model.get('animation.in.splitDelay'), this.model.get('animation.in').getTweenObj()).tweens;
			this.animation_in.refreshDuration()

			this.animation_out._render(0)
			this.animation_in._render(0)
		},
		exitPreview: function () {
			this.$animation.addClass('hidden')
			this.$container.removeClass('hidden')
		},
		previewAnimIn: function () {
			this.rootView.enterAnimationPreview();
			this.animation_in.tweens && this.rootView.slideAnim
				.play(this.animation_in.tweens[0].delay);
		},
		previewAnimOut: function () {
			this.rootView.enterAnimationPreview();
			this.animation_out.tweens && this.rootView.slideAnim
				.play(this.animation_out.tweens[0].delay);
		}
	});

	exports.ES_ItemsView = B.CollectionView.extend({
		itemView: ES_ItemView,
		constructor: function ES_ItemsView () {
			B.CollectionView.apply(this, arguments)
			this.activate()
		},
		activate: function () {
			this.activated = true;
			this.$el.addClass('es-activated');
		},
		deactivate: function () {
			this.activated = false;
			this.$el.removeClass('es-activated');
		}
	});

	exports.ES_GlobalItemsView = ES_ItemsView.extend({
		itemView: ES_ItemView.extend({
			constructor: function ES_GlobalItemView () {
				ES_ItemView.apply(this, arguments)
			}
		}),
		constructor: function ES_GlobalItemsView () {
			ES_ItemsView.apply(this, arguments)
			this.deactivate()
		},
	});

}(this, jQuery, _, JSNES_Backbone);