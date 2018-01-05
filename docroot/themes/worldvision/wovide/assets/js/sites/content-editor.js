;(function ($, Drupal) {
  /* prep - need to replace "default" modernizr
  Modernizr.load({
    test: Modernizr['object-fit'],
    nope: [
      'https://cdn.rawgit.com/anselmh/object-fit/master/dist/polyfill.object-fit.css',
      'https://cdn.rawgit.com/anselmh/object-fit/master/dist/polyfill.object-fit.min.js'
    ],
    complete: function(){
      if (!Modernizr['object-fit']) {
        objectFit.polyfill({
          selector: 'img', // this can be any CSS selector
          fittype: 'cover' // either contain, cover, fill or none
        });
      }
    }
  });


  Drupal.behaviors.wovideObjectFit = {
    attach: function (context) {
      if (!Modernizr.objectfit) {
        console.info('no of');
      }
      else {
        console.info('you got OF, sir');
      }
    }
  };
  */
})(jQuery, Drupal);
