/**
 * Created by michaelpetri on 18/09/16.
 */

(function ($, Drupal, top) {
  Drupal.behaviors.offcanvas_child = {
    /**
     * Attaches the off canvas behaviour.
     *
     * @param context
     * @param settings
     */
    attach: function (context, settings) {
      if (window.location == top.location) {
        var parser = document.createElement('a');

        parser.href = settings.offcanvas.parent_url;
        parser.hash = 'canvas=' + window.location.pathname;

        window.location = parser.href;
      }

      $('.off-canvas-back', context).once('off-canvas-back-button--initialized').bind('click', function (e) {
        e.preventDefault();
        top.offCanvasManager.close();
      });
    }
  }
})(jQuery, Drupal, top);

