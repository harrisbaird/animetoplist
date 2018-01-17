<?php $this->Html->setHeader('Search results for ' . h(ucwords($query))); ?>

<div id="cse" style="width: 100%;">Searching, please wait.</div>
<script src="http://www.google.com/jsapi" type="text/javascript"></script>
<script type="text/javascript"> 
  function parseQueryFromUrl () {
    var queryParamName = "q";
    var search = window.location.search.substr(1);
    var parts = search.split('&');
    for (var i = 0; i < parts.length; i++) {
      var keyvaluepair = parts[i].split('=');
      if (decodeURIComponent(keyvaluepair[0]) == queryParamName) {
        return decodeURIComponent(keyvaluepair[1].replace(/\+/g, ' '));
      }
    }
    return '';
  }
  google.load('search', '1', {language : 'en'});
  var _gaq = _gaq || [];
  _gaq.push(["_setAccount", "UA-250587-2"]);
  function _trackQuery(control, searcher, query) {
    var gaQueryParamName = "q";
    var loc = document.location;
    var url = [
      loc.pathname,
      loc.search,
      loc.search ? '&' : '?',
      gaQueryParamName == '' ? 'q' : encodeURIComponent(gaQueryParamName),
      '=',
      encodeURIComponent(query)
    ].join('');
    _gaq.push(["_trackPageview", url]);
  }
  google.setOnLoadCallback(function() {
    var customSearchControl = new google.search.CustomSearchControl('016523656801945331318:jmylaoo5eas');
    customSearchControl.setResultSetSize(google.search.Search.FILTERED_CSE_RESULTSET);
    customSearchControl.setSearchStartingCallback(null, _trackQuery);
    var options = new google.search.DrawOptions();
    options.enableSearchResultsOnly();     
    customSearchControl.draw('cse', options);
    var queryFromUrl = parseQueryFromUrl();
    if (queryFromUrl) {
      customSearchControl.execute(queryFromUrl);
    }
  }, true);
</script>
<link rel="stylesheet" href="http://www.google.com/cse/style/look/default.css" type="text/css" /> 
