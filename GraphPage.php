<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Bootstrap Simple Login Form</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
<style type="text/css">
    #mynetwork {
      width: 1200px;
      height: 800px;
      border: 1px solid lightgray;
    }
  </style>
<script>
    function getNodes(graph){
        var nodes = [];
        nodes.push(graph["ID"]);
        if (graph["connectedUsers"] && graph["connectedUsers"].length > 0)
        for(var user of graph["connectedUsers"]) {
            var currentNodes = getNodes(user);
            for (var currentNode of currentNodes) {
                if (!nodes.includes(currentNode)) {
                    nodes.push(currentNode);
                }
            }
        }
        return nodes;
    }

    function mapNodes(nodesArray){
        // [
        //             {id: 1, label: 'Node 1',color:'red'},
        //             {id: 2, label: 'Node 2'},
        //             {id: 3, label: 'Node 3'},
        //             {id: 4, label: 'Node 4'},
        //             {id: 5, label: 'Node 5'}
        //]
        var mappedNodes = [];
        var firstNode = true;
        for (var node of nodesArray){
            var mappedNode = {id: node, label: 'Node ' + node};
            if (firstNode){
                firstNode = false;
                mappedNode = {id: node, label: 'Node ' + node, color: 'blue'};
            }
            mappedNodes.push(mappedNode);
        }
        return mappedNodes;
    }

    function getEdges(graph){
        var edges = [];
        if (graph["connectedUsers"] && graph["connectedUsers"].length > 0)
        for(var user of graph["connectedUsers"]) {
            var edge = '{"from": '+graph["ID"]+', "to": '+user["ID"]+'}';
            edges.push(edge);
            var moreEdges = getEdges(user);
            for (var currentEdge of moreEdges) {
                if (!edges.includes(currentEdge)) {
                    edges.push(currentEdge);
                }
            }
        }
        return edges;
    }

    function mapEdges(edgesArray){
        // [
        //     {from: 1, to: 3},
        //     {from: 3, to: 1},
        //     {from: 1, to: 2},
        //     {from: 2, to: 4},
        //     {from: 2, to: 5},
        //     {from: 3, to: 3}
        // ]
        var mappedEdges = [];
        var mappedEdgesCopy = [];
        for (var edge of edgesArray){
            var mappedEdge = JSON.parse(edge);
            mappedEdges.push(mappedEdge);
            mappedEdgesCopy.push(mappedEdge);
        }
        // // remove duplicates
        // var alreadyRemoved = [];
        // for (var i=0; i<mappedEdgesCopy.length; i++){
        //     var edge1 =  mappedEdgesCopy[i];
        //     for (var j=0; j<mappedEdgesCopy.length; j++){
        //         var edge2 = mappedEdgesCopy[j];
        //         if (edge1.from == edge2.to && edge2.to == edge1.from){
        //             alreadyRemoved.push(j);
        //             if (!alreadyRemoved.includes(i) && i!=j){
        //                 console.log(alreadyRemoved);
        //                 console.log(i);
        //                 console.log(edge1);
        //                 mappedEdges.splice(i, 1);
        //             }
        //         }
        //     }
        // }
        return mappedEdges;
    }

    function getConnectedUsers(){
        var urlParams = new URLSearchParams(window.location.search);
        var email = urlParams.get('email');
        $.ajax({                                      
            url: 'GraphDB2.php',       
            type: "POST",
            data: { 
                email: email,
                password: "1234",
                funktionsname: "anliegende_nutzer_abfragen"
            } 
            }).done(function( msg ) {
                var graph = JSON.parse(msg);
                var nodes = [];
                var nodeArray = getNodes(graph);
                var mappedNodes = mapNodes(nodeArray);
                var nodes = new vis.DataSet(mappedNodes);
                // create an array with edges
                var edgesArray = getEdges(graph);
                var mappedEdges = mapEdges(edgesArray);
                var edges = new vis.DataSet(mappedEdges);

                // create a network
                var container = document.getElementById('mynetwork');
                var data = {
                    nodes: nodes,
                    edges: edges
                };
                var options = {};
                var network = new vis.Network(container, data, options);
            });
    }
</script>


</head>
<body onload="getConnectedUsers()">
<div id="mynetwork"></div>
<script type="text/javascript">
  // create an array with nodes
</script>
</body>
</html>
