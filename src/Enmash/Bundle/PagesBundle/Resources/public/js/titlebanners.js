+function ($) {
    'use strict';
    var TitleBanners = function(element) {
        this.$element = $(element);
        this.init()
    }

    TitleBanners.prototype.init = function() {
        this.$items = this.$element.find('.item a.banner-opener');
        this.$items.on('click',  function(e){
            e.preventDefault();
            var sectionId = $(e.target).parent().attr('data-section-id');
            if (sectionId) {
                var section = $(this).find('#bannersection' + sectionId);
                if (section) {
                    $(section).fadeIn();
                }
            }
        });

        this.$items.find('div.close-btn').parent().each(function(i, v){
            $(v).on('click', function(){
                $(this).parent().fadeOut();
            });
        });
    }

    function Plugin(option) {
        return this.each(function() {
            var $this   = $(this)
            var data    = $this.data('bs.titleBanners')
            var options = typeof option == 'object' && option

            if (!data && option == 'destroy') return
            if (!data) $this.data('bs.titleBanners', (data = new TitleBanners(this, options)))
            if (typeof option == 'string') data[option]()
        });
    }

    $.fn.titleBanners = Plugin
    $.fn.titleBanners.constructor = TitleBanners

}(jQuery);