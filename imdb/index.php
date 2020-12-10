<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Hind:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="assets/css/style.css" rel="stylesheet">
  </head>
  <body>

    <header id="header">
      <div class="container">
          <span id="mobile_menu"><i class="fa fa-bars"></i></span>
          <a href="/" id="logo" title="">
              <img src="assets/images/logo.png" alt="2Embed">
              <div class="clearfix"></div>
          </a>
          <div id="header_menu">
              <ul class="nav header_menu-list">
                  <li class="nav-item"><a href="#" title="Home">Home</a></li>
                  <li class="nav-item"><a href="#" title="Example">Example</a></li>
                  <li class="nav-item"><a href="#" title="Library">Library</a></li>
                  <li class="nav-item"><a href="#" title="Contact">Contact</a></li>
              </ul>
              <div class="clearfix"></div>
          </div>
      </div>
    </header>
    <section id="main-wrapper">
      <div class="container">
        <div class="home-top">
            <div class="home-description">
                <div class="intro"><img src="assets/images/intro.png" alt=""></div>
                <h2 class="heading-large mb-3">Get movie &amp; Tv Shows stream by IMDB, TMDB, Title/Year Best Video Api, the best streaming movies API in 2020.</h2>
                <h3 class="heading-small">2Embed crawls various websites and search engines to find movie streaming links which are then stored into our database and served to you through our API service. </h3>
            </div>
            <div class="home-services">
                <div class="home-services-list">
                    <a class="hs-item hs-imdb" data-content="hs-imdb">
                        <div class="item-cover"><img src="assets/images/01.jpg"></div>
                        <div class="detail">
                            <div class="hs-title">Get movies/shows with <span>IMDB id</span></div>
                            <div class="hs-btn">
                                <button class="btn btn-radius btn-primary btnBlock" data-id="one"><i class="fa fa-chevron-circle-down mr-2"></i>Show Now
                                </button>
                            </div>
                        </div>
                    </a>
                    <a class="hs-item hs-tmdb" data-content="hs-tmdb">
                        <div class="item-cover"><img src="assets/images/11.jpg"></div>
                        <div class="detail">
                            <div class="hs-title">Get movies/shows with <span>TMDB id</span></div>
                            <div class="hs-btn">
                                <button class="btn btn-radius btn-primary btnBlock" data-id="two"><i class="fa fa-chevron-circle-down mr-2"></i>Show Now
                                </button>
                            </div>
                        </div>
                    </a>
                    <a class="hs-item hs-info" data-content="hs-info">
                        <div class="item-cover"><img src="assets/images/12.jpg"></div>
                        <div class="detail">
                            <div class="hs-title">Get movies/shows with <span>Title-Year</span></div>
                            <div class="hs-btn">
                                <button class="btn btn-radius btn-primary btnBlock" data-id="three"><i class="fa fa-chevron-circle-down mr-2"></i>Show Now
                                </button>
                            </div>
                        </div>
                    </a>
                    <a class="hs-item hs-lib" href="">
                        <div class="item-cover"><img src="assets/images/22.jpg"></div>
                        <div class="detail">
                            <div class="hs-title">APICloud Sharing <span>Library</span></div>
                            <div class="hs-btn">
                                <button class="btn btn-radius btn-primary btnBlock" data-id="four"><i class="fa fa-chevron-circle-down mr-2"></i>Show Now
                                </button>
                            </div>
                        </div>
                    </a>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="block_content">
          <div class="home-content" data-value="one" id="hs-tmdb" style="">
              <div class="hc-container">
                  <div class="pane-close">
                      <button id="panel-close" type="button" class="btn btn-circle btn-light"><i class="fa fa-times"></i></button>
                  </div>
                  <div class="cc-top">
                      <div class="content-tabs">
                          <ul class="nav nav-tabs pre-tabs model-tabs">
                              <li class="nav-item"><a class="nav-link active" data-toggle="tab" id="hs-tmdb-nav-1" href="#hs-tmdb-tab-01"><i class="fa fa-film mr-2"></i>Movie</a></li>
                              <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#hs-tmdb-tab-02" id="hs-tmdb-nav-2"><i class="fa fa-tv mr-2"></i>TV Show</a>
                              </li>
                              <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#hs-tmdb-tab-03" id="hs-tmdb-nav-3"><i class="fa fa-leaf mr-2"></i>API</a></li>
                          </ul>
                      </div>
                  </div>
                  <div class="tab-content">
                      <div id="hs-tmdb-tab-01" class="tab-pane active">
                          <div class="tab-pane-content">
                              <div class="tpc-form">
                                  <h4 class="heading-large mb-4">Try <span class="highlight-text">TMDB id</span> To
                                      Get Movie Iframe</h4>
                                  <form class="preform">
                                      <div class="form-group">
                                          <label class="prelabel" for="tmdbid-movie">Enter tmdb id</label>
                                          <input type="text" class="form-control" id="tmdbid-movie" placeholder="ex: 299534">
                                      </div>
                                      <div class="form-group getit-btn mb-0">
                                          <button class="btn btn-lg btn-radius btn-primary">Get it<i class="fa fa-angle-right ml-3"></i></button>
                                      </div>
                                  </form>
                              </div>
                              <div class="tpc-player">
                                  <div class="media-player">
                                      <iframe width="560" height="315" data-src="https://www.2embed.ru/embed/tmdb/movie?id=299534" frameborder="0" allowfullscreen="" src="https://www.2embed.ru/embed/tmdb/movie?id=299534"></iframe>
                                  </div>
                                  <div class="d-block title text-center">PLAYING <span class="highlight-text" id="tmdb-movie-id-playing">299534</span>
                                  </div>
                              </div>
                              <div class="clearfix"></div>
                          </div>
                      </div>
                      <div id="hs-tmdb-tab-02" class="tab-pane">
                          <div class="tab-pane-content">
                              <div class="tpc-form">
                                  <h4 class="heading-large mb-4">Try <span class="highlight-text">TMDB id</span> To
                                      Get Show Iframe</h4>
                                  <form class="preform">
                                      <div class="form-group">
                                          <label class="prelabel" for="tmdbid-show-id">Enter tmdb id</label>
                                          <input type="text" class="form-control" id="tmdbid-show-id" placeholder="ex: 66788">
                                      </div>
                                      <div class="row">
                                          <div class="col-xl-6 col-lg-6 col-md-12">
                                              <div class="form-group">
                                                  <label class="prelabel" for="tmdbid-show-ss">Season</label>
                                                  <input type="text" class="form-control" id="tmdbid-show-ss" placeholder="ex: 1">
                                              </div>
                                          </div>
                                          <div class="col-xl-6 col-lg-6 col-md-12">
                                              <div class="form-group">
                                                  <label class="prelabel" for="tmdbid-show-ep">Episode</label>
                                                  <input type="text" class="form-control" id="tmdbid-show-ep" placeholder="ex: 1">
                                              </div>
                                          </div>
                                      </div>
                                      <div class="form-group getit-btn mb-0">
                                          <button class="btn btn-lg btn-radius btn-primary">Get it<i class="fa fa-angle-right ml-3"></i></button>
                                      </div>
                                  </form>
                              </div>
                              <div class="tpc-player">
                                  <div class="media-player">
                                      <iframe width="560" height="315" data-src="https://www.2embed.ru/embed/tmdb/tv?id=66788&amp;s=1&amp;e=1" frameborder="0" allowfullscreen="" src=""></iframe>
                                  </div>
                                  <div class="d-block title text-center">PLAYING <span class="highlight-text" id="tmdb-show-id-playing">66788</span>,
                                      <span class="text-white" id="tmdb-show-season-playing">Season: 1</span>, <span class="text-white" id="tmdb-show-episode-playing">Episode: 1</span>
                                  </div>
                              </div>
                              <div class="clearfix"></div>
                          </div>
                      </div>
                      <div id="hs-tmdb-tab-03" class="tab-pane tab-pane-api">
                          <div class="tab-pane-content">
                              <div class="tpc-form">
                                  <form class="preform">
                                      <div class="form-group" style="margin-bottom: 40px;">
                                          <h4 class="heading-medium mb-3"><span class="highlight-text">TMDB id movie</span> API</h4>
                                          <div class="api-line">
                                              URL API : https://www.2embed.ru/embed/tmdb/movie?id=<span class="text-warning">TMDB ID</span>
                                          </div>
                                      </div>
                                      <div class="form-group">
                                          <h4 class="heading-medium mb-3"><span class="highlight-text">TMDB id tv show</span>
                                              API</h4>
                                          <div class="api-line">
                                              URL API : https://www.2embed.ru/embed/tmdb/tv?id=<span class="text-warning">TMDB ID</span>&amp;s=<span class="text-warning">SEASON NUMBER</span>&amp;e=<span class="text-warning">EPISODE NUMBER</span>
                                          </div>
                                      </div>
                                  </form>
                              </div>
                              <div class="clearfix"></div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="home-content" data-value="two" id="hs-tmdb" style="">
              <div class="hc-container">
                  <div class="pane-close">
                      <button id="panel-close" type="button" class="btn btn-circle btn-light"><i class="fa fa-times"></i></button>
                  </div>
                  <div class="cc-top">
                      <div class="content-tabs">
                          <ul class="nav nav-tabs pre-tabs model-tabs">
                              <li class="nav-item"><a class="nav-link active" data-toggle="tab" id="hs-tmdb-nav-1" href="#hs-tmdb-tab-01"><i class="fa fa-film mr-2"></i>Movie</a></li>
                              <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#hs-tmdb-tab-02" id="hs-tmdb-nav-2"><i class="fa fa-tv mr-2"></i>TV Show</a>
                              </li>
                              <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#hs-tmdb-tab-03" id="hs-tmdb-nav-3"><i class="fa fa-leaf mr-2"></i>API</a></li>
                          </ul>
                      </div>
                  </div>
                  <div class="tab-content">
                      <div id="hs-tmdb-tab-01" class="tab-pane active">
                          <div class="tab-pane-content">
                              <div class="tpc-form">
                                  <h4 class="heading-large mb-4">Try <span class="highlight-text">TMDB id</span> To
                                      Get Movie Iframe</h4>
                                  <form class="preform">
                                      <div class="form-group">
                                          <label class="prelabel" for="tmdbid-movie">Enter tmdb id</label>
                                          <input type="text" class="form-control" id="tmdbid-movie" placeholder="ex: 299534">
                                      </div>
                                      <div class="form-group getit-btn mb-0">
                                          <button class="btn btn-lg btn-radius btn-primary">Get it<i class="fa fa-angle-right ml-3"></i></button>
                                      </div>
                                  </form>
                              </div>
                              <div class="tpc-player">
                                  <div class="media-player">
                                      <iframe width="560" height="315" data-src="https://www.2embed.ru/embed/tmdb/movie?id=299534" frameborder="0" allowfullscreen="" src="https://www.2embed.ru/embed/tmdb/movie?id=299534"></iframe>
                                  </div>
                                  <div class="d-block title text-center">PLAYING <span class="highlight-text" id="tmdb-movie-id-playing">299534</span>
                                  </div>
                              </div>
                              <div class="clearfix"></div>
                          </div>
                      </div>
                      <div id="hs-tmdb-tab-02" class="tab-pane">
                          <div class="tab-pane-content">
                              <div class="tpc-form">
                                  <h4 class="heading-large mb-4">Try <span class="highlight-text">TMDB id</span> To
                                      Get Show Iframe</h4>
                                  <form class="preform">
                                      <div class="form-group">
                                          <label class="prelabel" for="tmdbid-show-id">Enter tmdb id</label>
                                          <input type="text" class="form-control" id="tmdbid-show-id" placeholder="ex: 66788">
                                      </div>
                                      <div class="row">
                                          <div class="col-xl-6 col-lg-6 col-md-12">
                                              <div class="form-group">
                                                  <label class="prelabel" for="tmdbid-show-ss">Season</label>
                                                  <input type="text" class="form-control" id="tmdbid-show-ss" placeholder="ex: 1">
                                              </div>
                                          </div>
                                          <div class="col-xl-6 col-lg-6 col-md-12">
                                              <div class="form-group">
                                                  <label class="prelabel" for="tmdbid-show-ep">Episode</label>
                                                  <input type="text" class="form-control" id="tmdbid-show-ep" placeholder="ex: 1">
                                              </div>
                                          </div>
                                      </div>
                                      <div class="form-group getit-btn mb-0">
                                          <button class="btn btn-lg btn-radius btn-primary">Get it<i class="fa fa-angle-right ml-3"></i></button>
                                      </div>
                                  </form>
                              </div>
                              <div class="tpc-player">
                                  <div class="media-player">
                                      <iframe width="560" height="315" data-src="https://www.2embed.ru/embed/tmdb/tv?id=66788&amp;s=1&amp;e=1" frameborder="0" allowfullscreen="" src=""></iframe>
                                  </div>
                                  <div class="d-block title text-center">PLAYING <span class="highlight-text" id="tmdb-show-id-playing">66788</span>,
                                      <span class="text-white" id="tmdb-show-season-playing">Season: 1</span>, <span class="text-white" id="tmdb-show-episode-playing">Episode: 1</span>
                                  </div>
                              </div>
                              <div class="clearfix"></div>
                          </div>
                      </div>
                      <div id="hs-tmdb-tab-03" class="tab-pane tab-pane-api">
                          <div class="tab-pane-content">
                              <div class="tpc-form">
                                  <form class="preform">
                                      <div class="form-group" style="margin-bottom: 40px;">
                                          <h4 class="heading-medium mb-3"><span class="highlight-text">TMDB id movie</span> API</h4>
                                          <div class="api-line">
                                              URL API : https://www.2embed.ru/embed/tmdb/movie?id=<span class="text-warning">TMDB ID</span>
                                          </div>
                                      </div>
                                      <div class="form-group">
                                          <h4 class="heading-medium mb-3"><span class="highlight-text">TMDB id tv show</span>
                                              API</h4>
                                          <div class="api-line">
                                              URL API : https://www.2embed.ru/embed/tmdb/tv?id=<span class="text-warning">TMDB ID</span>&amp;s=<span class="text-warning">SEASON NUMBER</span>&amp;e=<span class="text-warning">EPISODE NUMBER</span>
                                          </div>
                                      </div>
                                  </form>
                              </div>
                              <div class="clearfix"></div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="home-content" data-value="three" id="hs-tmdb" style="">
              <div class="hc-container">
                  <div class="pane-close">
                      <button id="panel-close" type="button" class="btn btn-circle btn-light"><i class="fa fa-times"></i></button>
                  </div>
                  <div class="cc-top">
                      <div class="content-tabs">
                          <ul class="nav nav-tabs pre-tabs model-tabs">
                              <li class="nav-item"><a class="nav-link active" data-toggle="tab" id="hs-tmdb-nav-1" href="#hs-tmdb-tab-01"><i class="fa fa-film mr-2"></i>Movie</a></li>
                              <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#hs-tmdb-tab-02" id="hs-tmdb-nav-2"><i class="fa fa-tv mr-2"></i>TV Show</a>
                              </li>
                              <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#hs-tmdb-tab-03" id="hs-tmdb-nav-3"><i class="fa fa-leaf mr-2"></i>API</a></li>
                          </ul>
                      </div>
                  </div>
                  <div class="tab-content">
                      <div id="hs-tmdb-tab-01" class="tab-pane active">
                          <div class="tab-pane-content">
                              <div class="tpc-form">
                                  <h4 class="heading-large mb-4">Try <span class="highlight-text">TMDB id</span> To
                                      Get Movie Iframe</h4>
                                  <form class="preform">
                                      <div class="form-group">
                                          <label class="prelabel" for="tmdbid-movie">ENTER TITLE</label>
                                          <input type="text" class="form-control" id="tmdbid-movie" placeholder="ex: Legacy">
                                      </div>
                                      <div class="form-group">
                                          <label class="prelabel" for="tmdbid-movie">ENTER YEAR</label>
                                          <input type="text" class="form-control" id="tmdbid-movie" placeholder="ex: 2020">
                                      </div>
                                      <div class="form-group getit-btn mb-0">
                                          <button class="btn btn-lg btn-radius btn-primary">Get it<i class="fa fa-angle-right ml-3"></i></button>
                                      </div>
                                  </form>
                              </div>
                              <div class="tpc-player">
                                  <div class="media-player">
                                      <iframe width="560" height="315" data-src="https://www.2embed.ru/embed/tmdb/movie?id=299534" frameborder="0" allowfullscreen="" src="https://www.2embed.ru/embed/tmdb/movie?id=299534"></iframe>
                                  </div>
                                  <div class="d-block title text-center">PLAYING <span class="highlight-text" id="tmdb-movie-id-playing">299534</span>
                                  </div>
                              </div>
                              <div class="clearfix"></div>
                          </div>
                      </div>
                      <div id="hs-tmdb-tab-02" class="tab-pane">
                          <div class="tab-pane-content">
                              <div class="tpc-form">
                                  <h4 class="heading-large mb-4">Try <span class="highlight-text">TMDB id</span> To
                                      Get Show Iframe</h4>
                                  <form class="preform">
                                      <div class="form-group">
                                          <label class="prelabel" for="tmdbid-show-id">Enter tmdb id</label>
                                          <input type="text" class="form-control" id="tmdbid-show-id" placeholder="ex: 66788">
                                      </div>
                                      <div class="row">
                                          <div class="col-xl-6 col-lg-6 col-md-12">
                                              <div class="form-group">
                                                  <label class="prelabel" for="tmdbid-show-ss">Season</label>
                                                  <input type="text" class="form-control" id="tmdbid-show-ss" placeholder="ex: 1">
                                              </div>
                                          </div>
                                          <div class="col-xl-6 col-lg-6 col-md-12">
                                              <div class="form-group">
                                                  <label class="prelabel" for="tmdbid-show-ep">Episode</label>
                                                  <input type="text" class="form-control" id="tmdbid-show-ep" placeholder="ex: 1">
                                              </div>
                                          </div>
                                      </div>
                                      <div class="form-group getit-btn mb-0">
                                          <button class="btn btn-lg btn-radius btn-primary">Get it<i class="fa fa-angle-right ml-3"></i></button>
                                      </div>
                                  </form>
                              </div>
                              <div class="tpc-player">
                                  <div class="media-player">
                                      <iframe width="560" height="315" data-src="https://www.2embed.ru/embed/tmdb/tv?id=66788&amp;s=1&amp;e=1" frameborder="0" allowfullscreen="" src=""></iframe>
                                  </div>
                                  <div class="d-block title text-center">PLAYING <span class="highlight-text" id="tmdb-show-id-playing">66788</span>,
                                      <span class="text-white" id="tmdb-show-season-playing">Season: 1</span>, <span class="text-white" id="tmdb-show-episode-playing">Episode: 1</span>
                                  </div>
                              </div>
                              <div class="clearfix"></div>
                          </div>
                      </div>
                      <div id="hs-tmdb-tab-03" class="tab-pane tab-pane-api">
                          <div class="tab-pane-content">
                              <div class="tpc-form">
                                  <form class="preform">
                                      <div class="form-group" style="margin-bottom: 40px;">
                                          <h4 class="heading-medium mb-3"><span class="highlight-text">TMDB id movie</span> API</h4>
                                          <div class="api-line">
                                              URL API : https://www.2embed.ru/embed/tmdb/movie?id=<span class="text-warning">TMDB ID</span>
                                          </div>
                                      </div>
                                      <div class="form-group">
                                          <h4 class="heading-medium mb-3"><span class="highlight-text">TMDB id tv show</span>
                                              API</h4>
                                          <div class="api-line">
                                              URL API : https://www.2embed.ru/embed/tmdb/tv?id=<span class="text-warning">TMDB ID</span>&amp;s=<span class="text-warning">SEASON NUMBER</span>&amp;e=<span class="text-warning">EPISODE NUMBER</span>
                                          </div>
                                      </div>
                                  </form>
                              </div>
                              <div class="clearfix"></div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
        </div>
      </div>
    </section>
    <div id="footer">
        <div class="container">
            <div class="copyright">© Copyright</div>
            <div class="footer-links">
                <ul class="float-left ulclear">
                    <li><a href="/" title="Home">Home</a></li>
                    <li><a href="/" title="Example">Example</a></li>
                    <li><a href="/" title="Library">Library</a></li>
                    <li><a href="/" title="Contact">Contact</a></li>
                </ul>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

   

    



    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/archive.js"></script>
  </body>
</html>