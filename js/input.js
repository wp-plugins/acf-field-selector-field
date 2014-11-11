(function($){


	function initialize_field( $el ) {

		//$el.doStuff();

	}


	if( typeof acf.add_action !== 'undefined' ) {

		/*
		*  ready append (ACF5)
		*
		*  These are 2 events which are fired during the page load
		*  ready = on page load similar to $(document).ready()
		*  append = on new DOM elements appended via repeater field
		*
		*  @type	event
		*  @date	20/07/13
		*
		*  @param	$el (jQuery selection) the jQuery element which contains the ACF fields
		*  @return	n/a
		*/

		acf.add_action('ready append', function( $el ){

			// search $el for fields of type 'FIELD_NAME'
			acf.get_fields({ type : 'field_selector'}, $el).each(function(){

				initialize_field( $(this) );
	acf.fields.field_selector = {
		$el : null,
		$input : null,
		$left : null,
		$right : null,
		$data : null,

		o : {},

		timeout : null,

		set : function( o ){
			// merge in new option
			$.extend( this, o );
			// find elements
			this.$input = this.$el.children('input[type="hidden"]');
			this.$left = this.$el.find('.acffs-autocomplete-left'),
			this.$right = this.$el.find('.acffs-autocomplete-right');


			var data = []
			var items = this.$left.find( '.acffs-autocomplete-list > li > a' );
			$.each( items, function() {
				data.push( $(this).data('name') );
			})

			this.$data = data;

			// return this for chaining
			return this;

		},
		init : function(){

			// reference
			var _this = this;


			// is clone field?
			if( acf.helpers.is_clone_field(this.$input) )
			{
				return;
			}


			// set height of right column
			this.$right.find('.acffs-autocomplete-list').height( this.$left.height() -2 );


			// right sortable
			this.$right.find('.acffs-autocomplete-list').sortable({
				axis					:	'y',
				items					:	'> li',
				forceHelperSize			:	true,
				forcePlaceholderSize	:	true,
				scroll					:	true,
				update					:	function(){

					_this.$input.trigger('change');

				}
			});

		},
		render : function( json ){

			// reference
			var _this = this;


			// update classes
			this.$el.removeClass('no-results').removeClass('loading');


			// new search?
			if( this.o.paged == 1 )
			{
				this.$el.find('.acffs-autocomplete-left li:not(.load-more)').remove();
			}


			// no results?
			if( ! json || ! json.html )
			{
				this.$el.addClass('no-results');
				return;
			}


			// append new results
			this.$el.find('.acffs-autocomplete-left .load-more').before( json.html );


			// next page?
			if( ! json.next_page_exists )
			{
				this.$el.addClass('no-results');
			}


			// apply .hide to left li's
			this.$left.find('a').each(function(){

				var id = $(this).attr('data-value');

				if( _this.$right.find('a[data-value="' + id + '"]').exists() )
				{
					$(this).parent().addClass('hide');
				}

			});

		},
		add : function( $a ){

			// vars
			var id = $a.attr('data-value'),
				title = $a.html();

			// max posts
			if( this.o.max != '' && this.o.max != null && typeof this.o.max != 'undefined' ) {
			if( this.$right.find('a').length >= this.o.max )
			{
				alert( acf.l10n.field_selector.max.replace('{max}', this.o.max) );
				return false;
			}
			}

			// can be added?
			if( $a.parent().hasClass('hide') )
			{
				return false;
			}


			// hide
			$a.parent().addClass('hide');


			// template
			var data = {
					value		:	$a.attr('data-value'),
					title		:	$a.html(),
					name		:	this.$input.attr('name')
				},
				tmpl = _.template(acf.l10n.field_selector.tmpl_li, data);


			// add new li
			console.log(this.$right);
			this.$right.find('.acffs-autocomplete-list').append( tmpl )


			// trigger change on new_li
			this.$input.trigger('change');


			// validation
			this.$el.closest('.field').removeClass('error');


		},
		remove : function( $a ){

			// remove
			$a.parent().remove();


			// show
			this.$left.find('a[data-value="' + $a.attr('data-value') + '"]').parent('li').removeClass('hide');


			// trigger change on new_li
			this.$input.trigger('change');

		},
		filter : function( value ){
			// reference

			var _this = this,
				$el = this.$el,
				$data = _this.$data,
				indexes = [];

				value = value.toLowerCase();

			$.each( $data, function( i, item ) {
				item = item.toLowerCase();
				if( item.indexOf( value ) != -1 ) {
					indexes.push( i )
				}
			})

			var list = this.$left.find( '.acffs-autocomplete-list > li' );
			var elements = list.filter(function(i) {
			    return $.inArray(i, indexes) > -1;
			});

			list.hide();
			elements.show();

		},

	};

	/*
	*  acf/setup_fields
	*
	*  run init function on all elements for this field
	*
	*  @type	event
	*  @date	20/07/13
	*
	*  @param	{object}	e		event object
	*  @param	{object}	el		DOM object which may contain new ACF elements
	*  @return	N/A
	*/

	$(document).on('acf/setup_fields', function(e, el){
		$(el).find('.acffs-autocomplete-container').each(function(){

			acf.fields.field_selector.set({ $el : $(this) }).init();

		});

	});


	/*
	*  Events
	*
	*  jQuery events for this field
	*
	*  @type	function
	*  @date	1/03/2011
	*
	*  @param	N/A
	*  @return	N/A
	*/


	$(document).on('click', '.acffs-autocomplete-container .acffs-autocomplete-left .acffs-autocomplete-list a', function( e ){

		e.preventDefault();

		acf.fields.field_selector.set({ $el : $(this).closest('.acffs-autocomplete-container') }).add( $(this) );

		$(this).blur();

	});

	$(document).on('click', '.acffs-autocomplete-container .acffs-autocomplete-right .acffs-autocomplete-list a', function( e ){

		e.preventDefault();

		acf.fields.field_selector.set({ $el : $(this).closest('.acffs-autocomplete-container') }).remove( $(this) );

		$(this).blur();

	});

	$(document).on('keyup', '.acffs-autocomplete-container input.acffs-autocomplete-search', function( e ){
		// vars
		var val = $(this).val(),
			$el = $(this).closest('.acffs-autocomplete-container');
	    acf.fields.field_selector.set({ $el : $el }).filter( val );

	});

	$(document).on('keypress', '.acffs-autocomplete-container input.acffs-autocomplete-search', function( e ){

		// don't submit form
		if( e.which == 13 )
		{
			e.preventDefault();
		}

	});

	$(document).on( 'click', '.acf-tab-button', function() {
		$.each( $( '.acf_postbox .field .acffs-autocomplete-container' ), function() {
			$(this).find('.acffs-autocomplete-right .acffs-autocomplete-list').height( $(this).find('.acffs-autocomplete-left').height() - 1 )
		})
	})

			});

		});


	} else {


		/*
		*  acf/setup_fields (ACF4)
		*
		*  This event is triggered when ACF adds any new elements to the DOM.
		*
		*  @type	function
		*  @since	1.0.0
		*  @date	01/01/12
		*
		*  @param	event		e: an event object. This can be ignored
		*  @param	Element		postbox: An element which contains the new HTML
		*
		*  @return	n/a
		*/

		$(document).live('acf/setup_fields', function(e, postbox){

			$(postbox).find('.field[data-field_type="field_selector"]').each(function(){

				initialize_field( $(this) );
	acf.fields.field_selector = {
		$el : null,
		$input : null,
		$left : null,
		$right : null,
		$data : null,

		o : {},

		timeout : null,

		set : function( o ){
			// merge in new option
			$.extend( this, o );
			// find elements
			this.$input = this.$el.children('input[type="hidden"]');
			this.$left = this.$el.find('.acffs-autocomplete-left'),
			this.$right = this.$el.find('.acffs-autocomplete-right');

			// get options
			this.o = acf.helpers.get_atts( this.$el );

			var data = []
			var items = this.$left.find( '.acffs-autocomplete-list > li > a' );
			$.each( items, function() {
				data.push( $(this).data('name') );
			})

			this.$data = data;

			// return this for chaining
			return this;

		},
		init : function(){

			// reference
			var _this = this;


			// is clone field?
			if( acf.helpers.is_clone_field(this.$input) )
			{
				return;
			}


			// set height of right column
			this.$right.find('.acffs-autocomplete-list').height( this.$left.height() -2 );


			// right sortable
			this.$right.find('.acffs-autocomplete-list').sortable({
				axis					:	'y',
				items					:	'> li',
				forceHelperSize			:	true,
				forcePlaceholderSize	:	true,
				scroll					:	true,
				update					:	function(){

					_this.$input.trigger('change');

				}
			});

		},
		render : function( json ){

			// reference
			var _this = this;


			// update classes
			this.$el.removeClass('no-results').removeClass('loading');


			// new search?
			if( this.o.paged == 1 )
			{
				this.$el.find('.acffs-autocomplete-left li:not(.load-more)').remove();
			}


			// no results?
			if( ! json || ! json.html )
			{
				this.$el.addClass('no-results');
				return;
			}


			// append new results
			this.$el.find('.acffs-autocomplete-left .load-more').before( json.html );


			// next page?
			if( ! json.next_page_exists )
			{
				this.$el.addClass('no-results');
			}


			// apply .hide to left li's
			this.$left.find('a').each(function(){

				var id = $(this).attr('data-value');

				if( _this.$right.find('a[data-value="' + id + '"]').exists() )
				{
					$(this).parent().addClass('hide');
				}

			});

		},
		add : function( $a ){

			// vars
			var id = $a.attr('data-value'),
				title = $a.html();

			// max posts
			if( this.o.max != '' && this.o.max != null && typeof this.o.max != 'undefined' ) {
			if( this.$right.find('a').length >= this.o.max )
			{
				alert( acf.l10n.field_selector.max.replace('{max}', this.o.max) );
				return false;
			}
			}

			// can be added?
			if( $a.parent().hasClass('hide') )
			{
				return false;
			}


			// hide
			$a.parent().addClass('hide');


			// template
			var data = {
					value		:	$a.attr('data-value'),
					title		:	$a.html(),
					name		:	this.$input.attr('name')
				},
				tmpl = _.template(acf.l10n.field_selector.tmpl_li, data);


			// add new li
			console.log(this.$right);
			this.$right.find('.acffs-autocomplete-list').append( tmpl )


			// trigger change on new_li
			this.$input.trigger('change');


			// validation
			this.$el.closest('.field').removeClass('error');


		},
		remove : function( $a ){

			// remove
			$a.parent().remove();


			// show
			this.$left.find('a[data-value="' + $a.attr('data-value') + '"]').parent('li').removeClass('hide');


			// trigger change on new_li
			this.$input.trigger('change');

		},
		filter : function( value ){
			// reference

			var _this = this,
				$el = this.$el,
				$data = _this.$data,
				indexes = [];

				value = value.toLowerCase();

			$.each( $data, function( i, item ) {
				item = item.toLowerCase();
				if( item.indexOf( value ) != -1 ) {
					indexes.push( i )
				}
			})

			var list = this.$left.find( '.acffs-autocomplete-list > li' );
			var elements = list.filter(function(i) {
			    return $.inArray(i, indexes) > -1;
			});

			list.hide();
			elements.show();

		},

	};

	/*
	*  acf/setup_fields
	*
	*  run init function on all elements for this field
	*
	*  @type	event
	*  @date	20/07/13
	*
	*  @param	{object}	e		event object
	*  @param	{object}	el		DOM object which may contain new ACF elements
	*  @return	N/A
	*/

	$(document).on('acf/setup_fields', function(e, el){
		$(el).find('.acffs-autocomplete-container').each(function(){

			acf.fields.field_selector.set({ $el : $(this) }).init();

		});

	});


	/*
	*  Events
	*
	*  jQuery events for this field
	*
	*  @type	function
	*  @date	1/03/2011
	*
	*  @param	N/A
	*  @return	N/A
	*/


	$(document).on('click', '.acffs-autocomplete-container .acffs-autocomplete-left .acffs-autocomplete-list a', function( e ){

		e.preventDefault();

		acf.fields.field_selector.set({ $el : $(this).closest('.acffs-autocomplete-container') }).add( $(this) );

		$(this).blur();

	});

	$(document).on('click', '.acffs-autocomplete-container .acffs-autocomplete-right .acffs-autocomplete-list a', function( e ){

		e.preventDefault();

		acf.fields.field_selector.set({ $el : $(this).closest('.acffs-autocomplete-container') }).remove( $(this) );

		$(this).blur();

	});

	$(document).on('keyup', '.acffs-autocomplete-container input.acffs-autocomplete-search', function( e ){
		// vars
		var val = $(this).val(),
			$el = $(this).closest('.acffs-autocomplete-container');
	    acf.fields.field_selector.set({ $el : $el }).filter( val );

	});

	$(document).on('keypress', '.acffs-autocomplete-container input.acffs-autocomplete-search', function( e ){

		// don't submit form
		if( e.which == 13 )
		{
			e.preventDefault();
		}

	});

	$(document).on( 'click', '.acf-tab-button', function() {
		$.each( $( '.acf_postbox .field .acffs-autocomplete-container' ), function() {
			$(this).find('.acffs-autocomplete-right .acffs-autocomplete-list').height( $(this).find('.acffs-autocomplete-left').height() - 1 )
		})
	})



			});

		});


	}


})(jQuery);
