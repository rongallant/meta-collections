var metafield, metafieldSortable;

(function($) {
	metafield = {
		add_postbox_toggles : function(page, args) {
			
			var self = this;
			self.init(page, args);
			$('.meta-field-box h3, .meta-field-box .handlediv').bind('click.metafield', function() {
				var p = $(this).parent('.meta-field-box'), id = p.attr('id');

				if ( 'dashboard_browser_nag' == id )
					return;

				p.toggleClass('closed');
			
				if ( page != 'press-this' )
					self.save_state(page);

				if ( id ) {
					if ( !p.hasClass('closed') && $.isFunction(metafield.pbshow) )
						self.pbshow(id);
					else if ( p.hasClass('closed') && $.isFunction(metafield.pbhide) )
						self.pbhide(id);
				}
			});

			$('.meta-field-box h3 a').click( function(e) {
				e.stopPropagation();
			});

			$('.meta-field-box a.dismiss').bind('click.metafield', function(e) {
				var hide_id = $(this).parents('.meta-field-box').attr('id') + '-hide';
				$( '#' + hide_id ).prop('checked', false).triggerHandler('click');
				return false;
			});

			$('.hide-postbox-tog').bind('click.metafield', function() {
				var box = $(this).val();

				if ( $(this).prop('checked') ) {
					$('#' + box).show();
					if ( $.isFunction( metafield.pbshow ) )
						self.pbshow( box );
				} else {
					$('#' + box).hide();
					if ( $.isFunction( metafield.pbhide ) )
						self.pbhide( box );
				}
				self.save_state(page);
				self._mark_area();
			});

			$('.columns-prefs input[type="radio"]').bind('click.metafield', function(){
				var n = parseInt($(this).val(), 10);

				if ( n ) {
					self._pb_edit(n);
					self.save_order(page);
				}
			});
		},

		init : function(page, args) {
			var isMobile = $(document.body).hasClass('mobile');

			$.extend( this, args || {} );
			$('#wpbody-content').css('overflow','hidden');
			metafieldSortable = $('.meta-field-sortables').sortable({
				placeholder: 'sortable-placeholder',
				connectWith: '.meta-field-sortables',
				items: '.meta-field-box, .meta-field-system-box',
				handle: '.meta-field-hndle',
				cursor: 'move',
				delay: ( isMobile ? 200 : 0 ),
				distance: 2,
				tolerance: 'pointer',
				forcePlaceholderSize: true,
				helper: 'clone',
				opacity: 0.65,
				start: function(e,ui) {
				if ($('#ui_controls').length>0){
				jtabs.activate(0);
				}
				
				},
				stop: function(e,ui) {
					if ( $(this).find('#dashboard_browser_nag').is(':visible') && 'dashboard_browser_nag' != this.firstChild.id ) {
						$(this).sortable('cancel');
						
						return;
					}
					
					metafield.save_order(page);
				},
				receive: function(e,ui) {
					if ($(ui.item).parent().length>0){
					
					if($(ui.item).parent().attr('id')=="inactive-sortables"){
					$(ui.item).addClass('closed');	
					}else if($(this).hasClass("tableoverview")!=true){
					//$(ui.item).removeClass('closed');	
					}
					}
					
					if ( 'dashboard_browser_nag' == ui.item[0].id )
						$(ui.sender).sortable('cancel');

					metafield._mark_area();
				}
			});

			if ( isMobile ) {
				$(document.body).bind('orientationchange.metafield', function(){ metafield._pb_change(); });
				this._pb_change();
			}

			this._mark_area();
		},

		save_state : function(page) {
			var closed = $('.meta-field-box').filter('.closed').map(function() { return this.id; }).get().join(','),
				hidden = $('.meta-field-box').filter(':hidden').map(function() { return this.id; }).get().join(',');
				
			$.post(ajaxurl, {
				action: 'closed-metafield',
				closed: closed,
				hidden: hidden,
				closedmetafieldnonce: jQuery('#closedmetafieldnonce').val(),
				page: page
			});
		},
		
		save_order : function(page) {
			var postVars, page_columns = $('.columns-prefs input:checked').val() || 0;
			
			postVars = {
				action: this.action,
				_ajax_nonce: $('#meta-box-field-nonce').val(),
				page_columns: page_columns,
				page: page,
				post_type: this.post_type
			}
			
			
			$('.meta-field-sortables').each( function() {
				postVars["order[" + this.id.split('-')[0] + "]"] = $(this).sortable( 'toArray' ).join(',');
				//postVars["order[" + this.id.split('-')[0] + "]"] = $('.meta-field-sortables').sortable('serialize');
			} );
			
			if(this.action=="savetableoverview"){//aanpassen
			$.post(ajaxurl, postVars, function(data){
				setMessage(data);
			} );
			}
			
		},

		_mark_area : function() {
			var visible = $('div.meta-field-box:visible').length, side = $('#post-body #side-sortables');

			$('#dashboard-widgets .meta-field-sortables:visible').each(function(n, el){
				var t = $(this);

				if ( visible == 1 || t.children('.meta-field-box:visible').length )
					t.removeClass('empty-container');
				else
					t.addClass('empty-container');
			});

			if ( side.length ) {
				if ( side.children('.meta-field-box:visible').length )
					side.removeClass('empty-container');
				else if ( $('#postbox-container-1').css('width') == '280px' )
					side.addClass('empty-container');
			}
		},

		_pb_edit : function(n) {
			var el = $('.metabox-holder').get(0);
			el.className = el.className.replace(/columns-\d+/, 'columns-' + n);
		},

		_pb_change : function() {
			switch ( window.orientation ) {
				case 90:
				case -90:
					this._pb_edit(2);
					break;
				case 0:
				case 180:
					if ( $('#poststuff').length )
						this._pb_edit(1);
					else
						this._pb_edit(2);
					break;
			}
		},

		/* Callbacks */
		pbshow : false,

		pbhide : false
	};

}(jQuery));
