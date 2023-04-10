import $ from "jquery";
import DataTable from "datatables.net-dt";
import "datatables.net-dt/css/jquery.datatables.css";
const dayjs = require("dayjs");

$(function () {
    /**
     * The function adds a notice message to the page/form
     */
    const addNotice = ($element, notice) => {
        const element = $element.find(".notices");
        const notices = $(`<li>${notice.message}</li>`);
        notices.addClass([
            "notice",
            `notice-${notice.type ? notice.type : "info"}`,
        ]);

        element.append(notices);
    };

    const tableElement = $("#subscribers-table");
    if (tableElement) {
        // update the database value for the per_page.
        tableElement.on("length.dt", function (e, settings, len) {
            $.ajax({
                type: "PUT",
                accepts: {
                    text: "application/json",
                },
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
                url: "/api/settings",
                dataType: "json",
                data: { per_page: len },
            });
        });

        // remove any errors from previous requests.
        tableElement.on("preXhr.dt", function () {
            $(".notices li").remove();
        });

        // display the table
        const table = new DataTable(tableElement, {
            ajax: {
                url: "/api/subscribers",
                method: "GET",
                error: function (error, s, t, tt) {
                    if (error?.responseJSON?.message) {
                        const notices = $(document.body);
                        if (notices.length) {
                            addNotice(notices, {
                                type: "error",
                                message: error.responseJSON.message,
                            });
                            // display the no records message
                            table.context[0].oApi._fnDraw(table.context[0], 1);
                            // remove the loader.
                            table.context[0].oApi._fnProcessingDisplay(
                                table.context[0],
                                false
                            );
                        }
                    } else {
                        return error;
                    }
                },
            },
            processing: true,
            serverSide: true,
            ordering: false,
            pageLength: tableElement.data("per-page"),
            responsive: true,
            scrollX: true,
            autoWidth: false,
            columns: [
                {
                    data: "email",
                    render: (data) => {
                        if (!data) {
                            return;
                        }
                        return `<a href="/subscribers/${data}">${data}</a>`;
                    },
                },
                { data: "name" },
                {
                    data: "country",
                    render: (data, type, row) => {
                        if (!row.fields) {
                            return;
                        }

                        const value = row.fields.find(
                            (i) => i["key"] === "country"
                        );

                        return value?.value;
                    },
                },
                {
                    data: "date_subscribe",
                    render: (data) => {
                        if (data) {
                            const formatted = dayjs(data);
                            return formatted.format("DD/MM/YYYY");
                        }
                        return "";
                    },
                },
                {
                    data: "date_subscribe",
                    render: (data) => {
                        const formatted = dayjs(data);
                        return formatted.format("HH:mm");
                    },
                },
                {
                    render: (data, type, row) => {
                        return `<button type='button' class='delete' value='${
                            row.email
                        }'>${tableElement.data("lang-delete")}</button>`;
                    },
                },
            ],
        });
    }

    tableElement.on("click", ".delete", function () {
        const value = $(this).val();

        const dt = tableElement.DataTable();
        // show the table loader
        dt.context[0].oApi._fnProcessingDisplay(dt.context[0], true);

        $.ajax({
            type: "DELETE",
            accepts: {
                text: "application/json",
            },
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
            url: `/api/subscribers/${value}`,
            dataType: "json",
            complete: function (data, textStatus) {
                if (data.responseJSON?.message) {
                    if (data.status === 200) {
                        // redraw the table if successful.
                        dt.clear().draw();
                    }
                    const notices = $(document.body);
                    // display the message as a notice.
                    addNotice(notices, {
                        type: data.status !== 200 ? "error" : "success",
                        message: data.responseJSON.message,
                    });
                } else {
                    // should never get here.
                    alert(textStatus);
                    // hide the loader
                    dt.context[0].oApi._fnProcessingDisplay(
                        dt.context[0],
                        false
                    );
                }
            },
        });
    });

    /**
     * The function handles form submissions.
     */
    const formSubmit = (event) => {
        event.preventDefault();

        const $form = $(event.target);

        // remove any existing errors.
        $form.find(".has-error:input").removeClass("has-error");
        $(".notices").find(".notice").remove();

        // disable the form
        $form.find('[type="submit"]').prop("disabled", true);

        // kind of a cheat way to prevent the api_key placeholder from submitting.
        const data = $form
            .serialize()
            .replace("api_key=**********", "api_key=");

        // submit to the API
        $.ajax({
            type: $form.attr("method"),
            accepts: {
                text: "application/json",
            },
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
            url: $form.attr("action"),
            dataType: "json",
            data: data,
            success: function () {
                const message = $form.find(".form-success");
                if (message) {
                    addNotice($(document.body), {
                        type: "success",
                        message: message.html(),
                    });
                }
            },
            error: function (data) {
                if (data.responseJSON.errors) {
                    $.each(data.responseJSON.errors, (key, error) => {
                        addNotice($(document.body), {
                            type: "error",
                            message: error,
                        });
                    });
                } else {
                    addNotice($(document.body), {
                        type: "error",
                        message: data.responseText,
                    });
                }
            },
            complete: function () {
                // enable the form
                $form.find('[type="submit"]').prop("disabled", false);
            },
        });
    };

    // add the form submission handlers.
    $("form#settings,form#subscriber").submit(formSubmit);
});
