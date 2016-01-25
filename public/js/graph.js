var visualizator = function(data, options) {

    this.options = {};
    this.options.container = options.container;
    this.options.groupsOptions = options.groupsOptions || {};
    this.options.graphOptions = options.graphOptions || {};

    this.visualize = function() {
        var result = [];

        for (var key in data) {

            var graph = data[key],
             graphContainer = this.createGraphItem(graph.id, graph.title),
             groups = this.createGraphGroups(graph.groups),
             dataset = new vis.DataSet(graph.data),
             container = document.getElementById(graphContainer);

            var graphObject = new vis.Graph2d(container, dataset, groups, this.options.graphOptions);

            result.push(graphObject);
        }

        return result;
    };

    this.createGraphItem = function(id, title) {
        var sectionId = 'visualization-' + id;
        this.options.container.append(this.sectionTpl(sectionId, title));
        return sectionId;
    };

    this.sectionTpl = function(itemId, title) {
        return '<section>' +
            '<h4>' + title + '</h4>' +
            '<div id="' + itemId + '"></div>' +
         '</section>';
    };

    this.createGraphGroups = function(groupsData) {
        var groups = new vis.DataSet();

        for (var groupKey in groupsData) {
            var groupName = groupsData[groupKey];

            groups.add({
                id: groupName,
                content: groupName,
                options: this.options.groupsOptions
            });
        }

        return groups;
    }
};

