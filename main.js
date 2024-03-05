var btn = document.getElementById('btn');
var url = document.getElementById('ytb');
var link = document.getElementById('link');

function download() {
    btn.value = "Downloading..";
    btn.setAttribute("disabled", "disabled");
    link.innerHTML = "<img src='load.gif' style='width:100px'>";
    
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            set(JSON.parse(this.responseText));
        }
    };
    
    xhttp.onprogress = function(event) {
        if (event.lengthComputable) {
            var percentComplete = (event.loaded / event.total) * 100;
            document.getElementById('link').innerHTML = "Download progress: " + percentComplete.toFixed(2) + "%";
        }
    };
    
    xhttp.open("GET", "get.php?url=" + encodeURIComponent(url.value), true);
    xhttp.send();
}

function set(xml) {
    var responseElement = xml.getElementsByTagName('response')[0];
    if (responseElement) {
        var errorAttribute = responseElement.getAttribute('error');
        var message = responseElement.getElementsByTagName('message')[0];
        var url = responseElement.getElementsByTagName('url')[0];
        var image = responseElement.getElementsByTagName('image')[0];
        
        link.innerHTML = "";
        link.innerHTML += "<p>" + (message ? message.innerHTML : "") + "</p>";
        if (!errorAttribute) {
            link.innerHTML += "<img src='" + (image ? image.innerHTML : "") + "'/><br/>";
            link.innerHTML += "<audio controls><source src='" + (url ? url.innerHTML : "") + "' type='audio/mpeg'></audio><br/>";
            link.innerHTML += "<a target='_blank' href='" + (url ? url.innerHTML : "") + "'>Download MP3 File</a><br/>";
        }
    }
    
    btn.removeAttribute("disabled");
    btn.value = "Get MP3 file";
}
