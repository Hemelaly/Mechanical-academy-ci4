(function () {
    const tableState = new Map();

    function toBool(value, fallback) {
        if (value === null || value === undefined || value === "") {
            return fallback;
        }
        const normalized = String(value).trim().toLowerCase();
        return ["1", "true", "yes", "on"].includes(normalized);
    }

    function toInt(value, fallback) {
        const parsed = Number.parseInt(value, 10);
        return Number.isFinite(parsed) && parsed > 0 ? parsed : fallback;
    }

    function parsePerPageOptions(value) {
        if (!value) return [5, 10, 15, 20, 25, 50];
        const options = String(value)
            .split(",")
            .map((entry) => Number.parseInt(entry.trim(), 10))
            .filter((entry) => Number.isFinite(entry) && entry > 0);

        return options.length ? options : [5, 10, 15, 20, 25, 50];
    }

    function readConfig(table) {
        const searchable = toBool(table.dataset.datatableSearchable, true);
        const paging = toBool(table.dataset.datatablePaging, true);
        const sortable = toBool(table.dataset.datatableSortable, true);
        const fixedHeight = toBool(table.dataset.datatableFixedHeight, false);
        const perPage = toInt(table.dataset.datatablePerPage, 10);
        const perPageSelectEnabled = toBool(table.dataset.datatablePerPageSelect, true);
        const perPageSelect = perPageSelectEnabled ? parsePerPageOptions(table.dataset.datatablePerPageOptions) : false;

        return {
            searchable,
            paging,
            sortable,
            fixedHeight,
            perPage,
            perPageSelect,
            labels: {
                placeholder: "Pesquisar...",
                perPage: "{select} por pagina",
                noRows: "Nenhum dado encontrado",
                noResults: "Nenhum resultado para esta pesquisa",
                info: "Mostrando {start} a {end} de {rows} registros",
            },
        };
    }

    function createInstance(table, config) {
        if (!window.simpleDatatables || typeof window.simpleDatatables.DataTable !== "function") {
            return null;
        }

        if (!table || table.tagName !== "TABLE") {
            return null;
        }

        if (!table.tHead) {
            return null;
        }

        const instance = new window.simpleDatatables.DataTable(table, config);
        const wrapper = instance.wrapperDOM || table.closest(".datatable-wrapper");
        if (wrapper) {
            const top = wrapper.querySelector(".datatable-top");
            const bottom = wrapper.querySelector(".datatable-bottom");
            if (top && !config.searchable && config.perPageSelect === false) {
                top.style.display = "none";
            }
            if (bottom && !config.paging) {
                bottom.style.display = "none";
            }
        }
        tableState.set(table, { instance, config });
        return instance;
    }

    function initTable(table) {
        if (!table) return null;
        const existing = tableState.get(table);
        if (existing?.instance) return existing.instance;
        const config = readConfig(table);
        return createInstance(table, config);
    }

    function destroyTable(table) {
        const state = tableState.get(table);
        if (!state?.instance) return;
        state.instance.destroy();
        tableState.delete(table);
    }

    function refreshTable(table) {
        if (!table) return null;
        const state = tableState.get(table);
        const config = state?.config || readConfig(table);
        if (state?.instance) {
            state.instance.destroy();
            tableState.delete(table);
        }
        return createInstance(table, config);
    }

    function initAll(root) {
        const scope = root || document;
        const tables = scope.querySelectorAll("table[data-flowbite-datatable]");
        tables.forEach((table) => initTable(table));
    }

    window.FlowbiteDashboardTables = {
        initAll,
        initTable,
        refreshTable,
        destroyTable,
    };

    document.addEventListener("DOMContentLoaded", function () {
        initAll(document);
    });
})();
