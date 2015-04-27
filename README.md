# PHP Podcast Creator

Built with PHP, mySQL, and jQuery. 

This is an unofficial porting of Podcast Generator 1.4, which you can find here: http://podcastgen.sourceforge.net/index.php?lang=en

While waiting for the official release of the original Podcast Generator 2.0, I made a customized version that suited my needs a little better. Blatantly stolen from the original generator's website, this script features:

- Simplified installation;
- Free and open source;
- Supports ANY media filetype (mp3, ogg, mpg, m4v, mov, pdf, etc...) and allows to create mixed audio and video podcasts;
- The XML feed automatically generated is fully compatible with aggregators (e.g. Juice and iTunes), meets the w3c standards and supports iTunes specific tags;
- User friendly web administration interface: upload, edit, delete episodes and fully customize your podcast;
- Multilanguage support;
- Web upload of audio/video episodes;
- Web mp3 streaming player;
- Easy FTP support for episodes too large to upload via HTTP;

### New Features:
- HTML5 progress uploads (compatible with modern [not IE] browsers)
- HTML5 audio/video player (jPlayer)
- Significant structural changes that make updates and upgrades easier
- Feed download tracking
- Episode download tracking
- mySQL integration
- Bootstrap 3 integration for mobile-first compatibility

In an effort to streamline the original Generator, I removed categories, archives, freebox, the default themes, and several other components. I removed the reliance on prototype/scriptaculous and replaced that functionality with jQuery (via CDN). I improved error checking, and implemented Twitter Bootstrap 3 (also via CDN) as the main theme for easy customization. 

I swapped out the web player with jPlayer for yummy HTML5 audio and video. I implemented FontAwesome (also via CDN) for quick and easy icons. I removed a lot of clutter and cleaned up the files to make additions and modifications easier. I updated the RTE. 

## Installation Instructions
Upload all the files to your server. Create a directory called "media" in the root directory. Give the root directory, media and images folders all a CHMOD of 755. 

Download jPlayer (http://jplayer.org/) and put it in /components/player (so the structure should be /components/player/jquery.player.min.js). Download tinyMCE (http://www.tinymce.com/) and extract it into /components/tinymce (so the structure should be /components/tinymce/js/tinymce/jquery.tinymce.min.js). 

After that, just load your website and walk through the installation.

## Coming Soon
- Front-end rewrite

## Notes
The jPlayer video implementation still needs a little help, so any HTML5 video gurus are welcome to fork this project to work on optimizing this.

With this release I've totally abandoned the XML flat-storage of episode metadata and transitioned to mySQL for storing episode data. I've rewritten a lot of the feed regeneration process which speeds things up substantially for feeds with many episodes. With this release, there is likely no clear or easy update from the Podcast Generator to Podcast Creator. Sorry about that.