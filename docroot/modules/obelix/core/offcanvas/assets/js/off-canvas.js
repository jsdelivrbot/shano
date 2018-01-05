/**
 * Created by michaelpetri on 18/09/16.
 */
(function ($, Drupal, top) {
  Drupal.behaviors.offcanvas = {
    /**
     * The scotchPanel object.
     */
    scotchPanel: null,

    /**
     * Attaches the off canvas behaviour.
     *
     * @param context
     * @param settings
     */
    attach: function (context, settings) {
      top.offCanvasManager = top.offCanvasManager || this;

      $('body', context).once('offcanvas-initialized-page').each(this.initializeBody);
      $('a[target="offcanvas"]', context).once('offcanvas-trigger-init').each(this.initializeAnchor);
    },

    /**
     * Initializes the body element.
     */
    initializeBody: function () {
      $('<div class="offcanvas-overlay"></div>')
        .bind('click', top.offCanvasManager.close)
        .appendTo(this);

      top.addEventListener('hashchange', top.offCanvasManager.processLocationHash);

      top.addEventListener('offcanvas_close', function (e) {

      });

      if (top.location == window.location) {
        top.offCanvasManager.processLocationHash()
      }
    },

    /**
     * Tries to parse an canvas path from hash. If a hash was found it will be opend in
     */
    processLocationHash: function () {
      var url = top.location.hash.match(/^#canvas\=(.+)$/);
      if (url) {
        var scrollPosition = document.documentElement.scrollTop || document.body.scrollTop;
        if (typeof(scrollPosition) !== 'undefined') {
          window.scrollPosition = scrollPosition;
        }
        top.offCanvasManager.open(url[1]);
      }
    },

    /**
     * Initializes an anchor element.
     */
    initializeAnchor: function () {
      var href = this.pathname;

      if (top.location.host != this.host) {
        href = this.host + href;
      }

      href += this.search;

      this.setAttribute('href', top.location);
      this.hash = 'canvas=' + href;

      this.target = '_self';
    },

    /**
     * Closes the off canvas panel.
     */
    close: function () {
      top.offCanvasManager.scotchPanel.close();

      $(".scotch-panel-canvas").css('transform', 'initial'); // Fixes bug with bootstrap off-canvas navi
    },

    /**
     * Opens the off canvas panel.
     *
     * @param url
     *  The url to open in the panel.
     * @param settings
     *  Settings for the scotchPanel.
     *
     *  @todo implement drupalSettings
     */
    open: function (url, settings) {
      var parser = document.createElement('a');

      parser.setAttribute('href', url);
      parser.search += '&destination=' + window.location.pathname;

      var canvas = document.getElementById('offcanvas-container')
        .getElementsByTagName('iframe')[0];

      if (!canvas || canvas.src != parser.href) {
        $body = $('body');
        $offcanvasContainer = $('#offcanvas-container');

        $offcanvasContainer.find("iframe").remove();

        this.scotchPanel = $offcanvasContainer.scotchPanel({
          type: 'iframe',
          iframeURL: parser.getAttribute('href'),
          containerSelector: 'body',
          direction: 'right',
          duration: 750,
          transition: 'ease',
          distanceX: '85%',
          enableEscapeKey: true,
          beforePanelOpen: function () {
            $body.addClass('offcanvas-open');
          },
          beforePanelClose: function () {
            var scr = document.body.scrollTop;
            top.location.hash = '#';
            document.body.scrollTop = scr;
            $body.removeClass('offcanvas-open');
            if (typeof(window.scrollPosition) !== 'undefined') {
              var scrollPosition = window.scrollPosition;
              $(window).scrollTop(scrollPosition);
            }
          }
        });
      }

      top.offCanvasManager.scotchPanel.open();
    }
  }
})(jQuery, Drupal, top);
