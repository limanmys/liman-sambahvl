<script>

let myTree = {
    "MainServer": {
        "type": "Domain",
        "SubServer1": {
            "type": "Site",
            "AltServer1": {},
            "AltServer2": {}
        },
        "SubServer2": {
            "type": "Site",
            "AltServer5": {},
            "AltServer4": {},
            "AltServer3": {}
        },
        "SubServer3": {
            "AltServer6": {}
        },
        "SubServer4": {
            "type": "DC"
        }
    }
  }

  function getTreeJSON(){  

    var form = new FormData();
    request(API('get_tree_json'), form, function(response) {
        response = JSON.parse(response);
        message = JSON.parse(response.message);
        console.log(message);
        newTraverse(message, $(".tree"));

        $(".tree").find("ul").each(function (idx, elem) {
          if ($(elem).attr("data-type")) {
              if ($(elem).attr("data-type") == "Domain") {
                $(elem).parent().find("span").first().css("border", "2px #287ed6 solid");
              }
              if ($(elem).attr("data-type") == "Site") {
                $(elem).parent().find("span").first().css("border", "2px #98ff98 solid");
              }
              
              if ($(elem).attr("data-type") == "DC") {
                $(elem).parent().find("span").first().css("border", "2px #663399 solid");
              }
          }
          if(elem.innerHTML.trim() == "") {
            elem.remove();
          }
        })

    }, function(response) {
        let error = JSON.parse(response);
        showSwal(error.message, 'error', 3000);
    });
  }

  function newTraverse (obj, parent) {
    for (let item in obj) {
      addToTree(item, parent, obj[item].type)
      if (obj[item] !== null && typeof(obj[item]) == "object") {
        newTraverse(obj[item], $(`ul[data-id="${item}"]`));
      }
    }
  }

  function addToTree(key, parent, type) {
    if (key == "type") {
      return;
    }
    if (type) {
      parent.append(`<li><span>${key}</span><ul data-id="${key}" data-type="${type}"></ul></li>`);
    } else {
      parent.append(`<li><span>${key}</span><ul data-id="${key}"></ul></li>`);
    }
  }

  setTimeout(() => {
    getTreeJSON();
  }, 300);
</script>

<ul class="tree">
</ul>

<style>
.tree,
.tree ul,
.tree li {
    list-style: none;
    margin: 0;
    padding: 0;
    position: relative;
}

.tree {
    margin: 0 0 1em;
    text-align: center;
}

.tree,
.tree ul {
    display: table;
}

.tree ul {
    width: 100%;
}

.tree li {
    display: table-cell;
    padding: .5em 0;
    vertical-align: top;
}

.tree li:before {
    outline: solid 1px #1a202c;
    content: "";
    left: 0;
    position: absolute;
    right: 0;
    top: 0;
}

.tree li:first-child:before {
    left: 50%;
}

.tree li:last-child:before {
    right: 50%;
}

.tree code,
.tree span {
    border: solid 2px #1a202c;
    border-radius: .2em;
    display: inline-block;
    margin: 0 .2em .5em;
    padding: .2em .5em;
    position: relative;
}

.tree ul:before,
.tree code:before,
.tree span:before {
    outline: solid 1px #1a202c;
    content: "";
    height: .5em;
    left: 50%;
    position: absolute;
}

.tree ul:before {
    top: -.5em;
}

.tree code:before,
.tree span:before {
    top: -.55em;
}

.tree>li {
    margin-top: 0;
}

.tree>li:before,
.tree>li:after,
.tree>li>code:before,
.tree>li>span:before {
    outline: none;
}
</style>