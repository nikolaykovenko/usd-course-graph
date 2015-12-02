$(function() {

    for (var graphType in groupsRaw) {

        var groups = new vis.DataSet();
        for (var key in groupsRaw[graphType]) {
            var group = groupsRaw[graphType][key];

            groups.add({
                id: group,
                content: group,
                options: {
                    drawPoints: {
                        style: 'circle'
                    }
                }
            });
        }

        var dataset = new vis.DataSet(items[graphType]);
        var options = {
            legend: true
        };

        var container = document.getElementById('visualization-' + graphType);
        var graph2d = new vis.Graph2d(container, dataset, groups, options);
    }
});
