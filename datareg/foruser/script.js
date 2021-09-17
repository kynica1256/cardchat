fetch(window.location.protocol+"//"+window.location.hostname+"/chatproject/datareg/Cookie/check/").
        then(resp => {return resp.text()}).then(resBody => {
            console.log(resBody)
            if (resBody == "false") {

            } else {
                const jsonob = JSON.parse(resBody);
                const key = Object.keys(jsonob);
                for (let i of key) {
                    document.getElementById(i).value = jsonob[i];
                }
            }
})



savebutton.addEventListener("click", func);




function func() {
    const u = document.querySelector('#color').value;
    $.ajax({
        url: window.location.protocol+"//"+window.location.hostname+"/chatproject/datareg/",
        type: 'POST',
        data: {
    	    "host":document.getElementById("host").value,
    	    "login":document.getElementById("login").value,
    	    "password":document.getElementById("password").value,
    	    "database":document.getElementById("database").value,
    	    "name":window.btoa(unescape(encodeURIComponent(document.getElementById("name").value))),
    	    "token":document.getElementById("token").value,
    	    "id":document.getElementById("id").value,
            "color":u,
    	    "img":image,
    	    "imgname":namefile,
         },
        success: function (response) {
            console.log(response)
            document.querySelector('#blocknew').removeAttribute('style');
            document.querySelector('#exampleFormControlTextarea1').value = '<iframe src="'+window.location.protocol+'//'+window.location.hostname+'/chatproject/templates/ru/'+'" height="600px" width="500px" style="border: none;"></iframe>';
            document.querySelector('#exampleFormControlTextarea2').value = '<iframe src="'+window.location.protocol+'//'+window.location.hostname+'/chatproject/templates/eng/'+'" height="600px" width="500px" style="border: none;"></iframe>';
        },
        error: function () {
            alert('error');
        }
    });
    $.ajax({
        url: window.location.protocol+"//"+window.location.hostname+"/chatproject/datareg/Cookie/",
        type: 'POST',
        data: {
            "host":document.getElementById("host").value,
            "login":document.getElementById("login").value,
            "password":document.getElementById("password").value,
            "database":document.getElementById("database").value,
            "name":window.btoa(unescape(encodeURIComponent(document.getElementById("name").value))),
            "token":document.getElementById("token").value,
            "id":document.getElementById("id").value,
         },
        success: function (response) {
            console.log(response)
        },
        error: function () {
            alert('error');
        }
    });
}

function encodeImageFileAsURLmem(element) {
    var file = element.files[0];
    window.namefile = file.name;
    var reader = new FileReader();
    reader.onloadend = function() {
        window.image = reader.result;
    }
    reader.readAsDataURL(file);
}