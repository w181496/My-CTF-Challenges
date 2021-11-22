function getCats() {
    var xhr = new XMLHttpRequest();
    xhr.onload = function(e) {
        var data = JSON.parse(xhr.response);
        console.log(data[0]['url']);
        var url = data[0]['url'];
        var img = document.createElement("img");
        img.src = url;
        document.body.appendChild(img);
    }
    xhr.open("GET", "https://api.thecatapi.com/v1/images/search");
    xhr.send();
}
