function getArrayFromUrl (url) {
  let arrayFromUrl = url.split('?');
  arrayFromUrl = arrayFromUrl[1];
  arrayFromUrl = arrayFromUrl.split('&');
  let optionsFromUrl = {};
  let filter_type = '';
  for (i = 0; i < arrayFromUrl.length; i++) {
    let pairFromUrl = arrayFromUrl[i].split('=');
    if(i%2 === 0) {
      if (pairFromUrl[0] !== filter_type) {
        filter_type = pairFromUrl[0];
        optionsFromUrl[filter_type] = {values : [], names : []};
      }
    }
    else {
      optionsFromUrl[filter_type].values.push(pairFromUrl[0])
      optionsFromUrl[filter_type].names.push(pairFromUrl[1].replace("%20", " "))
    }
  }
  for (key in optionsFromUrl) {
    let filter_type = key;
    let values = optionsFromUrl[key]['values'];
    let names = optionsFromUrl[key]['names'];
    setFilterOptionsND(values, filter_type, names, takeOptionsFromUrl = true);
  }
}

function parsingUrlString(){
  var fullUrl = window.location.href;
  if (fullUrl.indexOf('?') > 0) {
    getArrayFromUrl(fullUrl);
  }
}

function updateUrl () {
  if (history.pushState) {
    var baseUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;;
    if(options.length) {
      var addUrl = '?';
      options.forEach(item => {
        filterName = item.filter;
        for(let i = 0; i < item.value.length; i++) {
          addUrl += `${filterName}=${item.value[i]}&`
          addUrl += `${item.value[i]}=${item.names[i]}&`
        }
      })
      addUrl = addUrl.substring(0, addUrl.length - 1);
      var newUrl = baseUrl + addUrl;
      history.pushState(null, null, newUrl);
    }
  }
  else {
    console.warn('History API не поддерживается');
  }
}
