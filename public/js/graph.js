$(function() {

    var groups = new vis.DataSet();
    for (var key in groupsRaw) {
        var group = groupsRaw[key];

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

    var dataset = new vis.DataSet(items);
    var options = {
        legend: true
    };

    var container = document.getElementById('visualization');
    var graph2d = new vis.Graph2d(container, dataset, groups, options);
});
