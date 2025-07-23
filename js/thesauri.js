/**
 * Keyword thesaurus setup for jsTree + Tagify modals.
 * Refactored for testability and modularity.
 */

export const keywordConfigurations = [
    {
        inputId: '#input-sciencekeyword',
        jsonFile: 'json/thesauri/gcmdScienceKeywords.json',
        jsTreeId: '#jstree-sciencekeyword',
        searchInputId: '#input-sciencekeyword-thesaurussearch',
        selectedKeywordsListId: 'selected-keywords-gcmd'
    },
    {
        inputId: '#input-Platforms',
        jsonFile: 'json/thesauri/gcmdPlatformsKeywords.json',
        jsTreeId: '#jstree-Platforms',
        searchInputId: '#input-Platforms-thesaurussearch',
        selectedKeywordsListId: 'selected-keywords-Platforms-gcmd'
    },
    {
        inputId: '#input-Instruments',
        jsonFile: 'json/thesauri/gcmdInstrumentsKeywords.json',
        jsTreeId: '#jstree-instruments',
        searchInputId: '#input-instruments-thesaurussearch',
        selectedKeywordsListId: 'selected-keywords-instruments-gcmd'
    },
    {
        inputId: '#input-mslkeyword',
        jsonFile: 'json/thesauri/msl-vocabularies.json',
        jsTreeId: '#jstree-mslkeyword',
        searchInputId: '#input-mslkeyword-thesaurussearch',
        selectedKeywordsListId: 'selected-keywords-msl'
    }
];

export function refreshThesaurusTagifyInstances($, translations) {
    keywordConfigurations.forEach(config => {
        const inputElement = document.querySelector(config.inputId);
        if (!inputElement || !inputElement._tagify) return;

        if (translations?.keywords?.thesaurus) {
            inputElement._tagify.settings.placeholder = translations.keywords.thesaurus.label;
            const placeholderElement = inputElement.parentElement.querySelector('.tagify__input');
            if (placeholderElement) {
                placeholderElement.setAttribute('data-placeholder', translations.keywords.thesaurus.label);
            }
        }
    });
}

export function initializeKeywordInput($, config, Tagify, translations) {
    var input = $(config.inputId)[0];
    var suggestedKeywords = [];

    function findNodeById(nodes, id) {
        for (var i = 0; i < nodes.length; i++) {
            if (nodes[i].id === id) return nodes[i];
            if (nodes[i].children) {
                const found = findNodeById(nodes[i].children, id);
                if (found) return found;
            }
        }
        return null;
    }

    function processNodes(nodes) {
        return nodes.map(function (node) {
            if (node.children) {
                node.children = processNodes(node.children);
            }
            node.a_attr = { title: node.description };
            node.original = {
                scheme: node.scheme || "",
                schemeURI: node.schemeURI || "",
                language: node.language || ""
            };
            return node;
        });
    }

    function buildWhitelist(data, parentPath = []) {
        data.forEach(function (item) {
            var textToAdd = parentPath.concat(item.text).join(' > ');
            suggestedKeywords.push({
                value: textToAdd,
                id: item.id,
                scheme: item.scheme,
                schemeURI: item.schemeURI,
                language: item.language
            });
            if (item.children) {
                buildWhitelist(item.children, parentPath.concat(item.text));
            }
        });
    }

    function findNodeByPath(jsTree, path) {
        return jsTree.get_json("#", { flat: true }).find(n => jsTree.get_path(n, " > ") === path);
    }

    $.getJSON(config.jsonFile, function (response) {
        const data = response.data || response;
        let filteredData = data;

        if (config.rootNodeId) {
            const selectedNode = findNodeById(data, config.rootNodeId);
            if (selectedNode) {
                filteredData = selectedNode.children || [];
            } else {
                console.error(`Root node with ID ${config.rootNodeId} not found in ${config.jsonFile}`);
                return;
            }
        }

        const processedData = processNodes(filteredData);
        buildWhitelist(filteredData);

        const tagify = new Tagify(input, {
            whitelist: suggestedKeywords,
            enforceWhitelist: true,
            placeholder: translations.keywords.thesaurus.label,
            dropdown: {
                maxItems: 50,
                enabled: 3,
                closeOnSelect: true,
                classname: "thesaurus-tagify"
            },
            editTags: false
        });

        input._tagify = tagify;

        $(config.jsTreeId).jstree({
            core: {
                data: processedData,
                themes: { icons: false }
            },
            checkbox: {
                keep_selected_style: true,
                three_state: false
            },
            plugins: ['search', 'checkbox'],
            search: {
                show_only_matches: true,
                search_callback: function (str, node) {
                    return node.text.toLowerCase().includes(str.toLowerCase()) ||
                        (node.a_attr?.title?.toLowerCase().includes(str.toLowerCase()));
                }
            }
        });

        function updateSelectedKeywordsList() {
            let selectedKeywordsList = document.getElementById(config.selectedKeywordsListId);
            if (!selectedKeywordsList) return;

            selectedKeywordsList.innerHTML = "";
            var selectedNodes = $(config.jsTreeId).jstree("get_selected", true);

            selectedNodes.forEach(function (node) {
                let fullPath = $(config.jsTreeId).jstree().get_path(node, " > ");
                let listItem = document.createElement("li");
                listItem.classList.add("list-group-item", "d-flex", "justify-content-between", "align-items-center");
                listItem.textContent = fullPath;

                let removeButton = document.createElement("button");
                removeButton.classList.add("btn", "btn-sm", "btn-danger");
                removeButton.innerHTML = "&times;";
                removeButton.onclick = function () {
                    $(config.jsTreeId).jstree("deselect_node", node.id);
                };

                listItem.appendChild(removeButton);
                selectedKeywordsList.appendChild(listItem);
            });
        }

        $(config.jsTreeId).on("changed.jstree", function (e, data) {
            updateSelectedKeywordsList();

            var selectedNodes = $(config.jsTreeId).jstree("get_selected", true);
            var selectedValues = selectedNodes.map(node => data.instance.get_path(node, " > "));

            tagify.removeAllTags();
            tagify.addTags(selectedValues);
        });

        tagify.on('add', function (e) {
            const tagText = e.detail.data.value;
            const jsTree = $(config.jsTreeId).jstree(true);
            const node = findNodeByPath(jsTree, tagText);
            if (node) jsTree.select_node(node.id);
        });

        tagify.on('remove', function (e) {
            const tagText = e.detail.data.value;
            const jsTree = $(config.jsTreeId).jstree(true);
            const node = findNodeByPath(jsTree, tagText);
            if (node) jsTree.deselect_node(node.id);
        });
    });
}

export function handleSearchInputEvent(e, $) {
    const searchInputId = `#${e.target.id}`;
    const config = keywordConfigurations.find(c => c.searchInputId === searchInputId);
    if (config && $(config.jsTreeId).jstree(true)) {
        $(config.jsTreeId).jstree(true).search($(e.target).val());
    }
}

export function handleSearchEnterKey(e, $) {
    if (e.key === 'Enter') {
        e.preventDefault();
        e.stopPropagation();
        const searchInput = $(e.target);
        const config = keywordConfigurations.find(c => c.searchInputId === `#${e.target.id}`);
        if (!config) return;
        const jsTreeInstance = $(config.jsTreeId).jstree(true);
        if (jsTreeInstance) jsTreeInstance.search(searchInput.val());
        searchInput.focus();
    }
}

export function initThesaurus($, Tagify, translations) {
    keywordConfigurations.forEach(config => {
        if ($(config.inputId).length) {
            initializeKeywordInput($, config, Tagify, translations);
        }
    });

    $(document).on('input', '[id$="-thesaurussearch"]', (e) => handleSearchInputEvent(e, $));
    $(document).on('keydown', '[id$="-thesaurussearch"]', (e) => handleSearchEnterKey(e, $));

    document.addEventListener('translationsLoaded', () => refreshThesaurusTagifyInstances($, translations));
}