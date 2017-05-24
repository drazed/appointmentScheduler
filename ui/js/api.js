// location of our Zend Expressive API
var apiurl = 'http://grouphealth.anyx.org/api';

/**
 * API calling factory, handles all api calls
 *
 * @param (obj) $http           used to make GET/POST/PUT/DELETE calls
 * @param (obj) $stateParams    GET/POST
 * @return (array)
 **/
function API($http) {
    return {
        apiurl : function() {
            return apiurl;
        },
        ping : function () {
            return $http({
                url : apiurl + '/ping',
                method : "GET",
            })
            .then(function (response) {
                return response.data;
            }, function (error) {
                return false;
            });
        },
        appointment : function () {
            return {
                get : function (appointment_id) {
                    // id can be null to fetch list, but should
                    // just exclude id rather then send null
                    if (!appointment_id) { appointment_id = ""; }

                    // return ALL appointments
                    return $http.get (apiurl + '/appointment/'+appointment_id)
                    .then(function (response) {
                        return response.data.appointments;
                    },
                    function (error) {
                        return false;
                    });
                },

                add : function (appointment_id, reason, date, start, end) {
                    data = { };
                    data.id = appointment_id;
                    data.reason = reason;
                    data.date = date;
                    data.start = start;
                    data.end = end;
                    return $http.post (apiurl + '/appointment/', data, {
                        headers : {
                            'Authorization' : 'token ' + sessionStorage.access_token
                        }
                    })
                    .then(function (response) {
                        return response;
                    },
                    function (error) {
                        return false;
                    });
                },
                put : function (appointment_id, reason, date, start, end) {
                    data = { };
                    data.reason = reason;
                    data.date = date;
                    data.start = start;
                    data.end = end;
                    return $http.put (apiurl + '/appointment/'+appointment_id, data, {
                        headers : {
                            'Authorization' : 'token ' + sessionStorage.access_token
                        }
                    })
                    .then(function (response) {
                        return response;
                    },
                        function (error) {
                        return false;
                    });
                },
                remove : function (appointment_id) {
                    return $http.delete (apiurl + '/appointment/'+appointment_id)
                    .then(function (response) {
                        return response.data;
                    }, function (error) {
                        return false;
                    });
                },
            }
        },
    }
}

angular
.module("grouphealth")
.factory("API",API)
