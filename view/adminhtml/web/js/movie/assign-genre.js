define([
    'mage/adminhtml/grid'
], function () {
    'use strict';

    return function (config) {
        var selectedGenres = config.selectedGenres,
            movieGenres = $H(selectedGenres),
            gridJsObject = window[config.gridJsObjectName],
            tabIndex = 1000;

        $('movie_genres').value = Object.toJSON(movieGenres);

        /**
         * Register Movie Genre
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerMovieGenre(grid, element, checked) {
            if (checked) {
                if (element.positionElement) {
                    element.positionElement.disabled = false;
                    movieGenres.set(element.value, element.positionElement.value);
                }
            } else {
                if (element.positionElement) {
                    element.positionElement.disabled = true;
                }
                movieGenres.unset(element.value);
            }
            $('in_movie_genres').value = Object.toJSON(movieGenres);
            grid.reloadParams = {
                'selected_genres[]': movieGenres.keys()
            };
        }

        /**
         * Click on component row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function movieGenreRowClick(grid, event) {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;

            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');

                if (checkbox[0]) {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }

        /**
         * Change component position
         *
         * @param {String} event
         */
        function positionChange(event) {
            var element = Event.element(event);

            if (element && element.checkboxElement && element.checkboxElement.checked) {
                movieGenres.set(element.checkboxElement.value, element.value);
                $('in_movie_genres').value = Object.toJSON(movieGenres);
            }
        }

        /**
         * Initialize mosaic component row
         *
         * @param {Object} grid
         * @param {String} row
         */
        function movieGenreRowInit(grid, row) {
            var checkbox = $(row).getElementsByClassName('checkbox')[0],
                position = $(row).getElementsByClassName('input-text')[0];

            if (checkbox && position) {
                checkbox.positionElement = position;
                position.checkboxElement = checkbox;
                position.disabled = !checkbox.checked;
                position.tabIndex = tabIndex++;
                Event.observe(position, 'keyup', positionChange);
            }
        }

        gridJsObject.rowClickCallback = movieGenreRowClick;
        gridJsObject.initRowCallback = movieGenreRowInit;
        gridJsObject.checkboxCheckCallback = registerMovieGenre;

        if (gridJsObject.rows) {
            gridJsObject.rows.each(function (row) {
                movieGenreRowInit(gridJsObject, row);
            });
        }
    };
});
