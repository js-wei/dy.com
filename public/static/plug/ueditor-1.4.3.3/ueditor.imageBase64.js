/**
 * @Author: 魏巍
 * @Date:   2017-12-11T11:15:39+08:00
 * @Email:  524314430@qq.com
 * @Last modified by:   魏巍
 * @Last modified time: 2017-12-11T12:01:43+08:00
 */
window.imageBase64 = {
  getBase64: function(src) {
    let _this = this;
    console.log(_this.canonical_uri(src));
    var img = document.createElement('img');
    img.src = src;
    img.onload = function() {
      var data = _this.getBase64Image(img);
      console.log(data);
    }
  },
  getBase64Image: function(img) {
    var canvas = document.createElement("canvas");
    canvas.width = img.width;
    canvas.height = img.height;

    var ctx = canvas.getContext("2d");
    ctx.drawImage(img, 0, 0, img.width, img.height);

    var dataURL = canvas.toDataURL("image/png", 0.5);
    return dataURL;
  },
  canonical_uri: function(src, base_path) {
    var root_page = /^[^?#]*\//.exec(location.href)[0],
      root_domain = /^\w+\:\/\/\/?[^\/]+/.exec(root_page)[0],
      absolute_regex = /^\w+\:\/\//;

    // is `src` is protocol-relative (begins with // or ///), prepend protocol
    if (/^\/\/\/?/.test(src)) {
      src = location.protocol + src;
    }
    // is `src` page-relative? (not an absolute URL, and not a domain-relative path, beginning with /)
    else if (!absolute_regex.test(src) && src.charAt(0) != "/") {
      // prepend `base_path`, if any
      src = (base_path || "") + src;
    }

    // make sure to return `src` as absolute
    return absolute_regex.test(src) ? src : ((src.charAt(0) == "/" ? root_domain : root_page) + src);
  }
}
