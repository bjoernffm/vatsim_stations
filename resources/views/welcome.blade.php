<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>VATSIM</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
        <script src="https://unpkg.com/vue@next"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.0/axios.min.js" integrity="sha512-DZqqY3PiOvTP9HkjIWgjO6ouCbq+dxqWoJZ/Q+zPYNHmlnI2dQnbJ5bxAHpAMw+LXRm4D72EIRXzvcHQtE8/VQ==" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous"></script>
    </head>
    <body>
        <div id="mountPoint">
            <div class="text-center pt-5" v-if="loading">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div v-if="!loading">
                <div class="card">
                    <ul class="list-group list-group-flush">
                        <template v-for="station in stations" :key="station">
                            <li class="list-group-item" v-if="station.online == true || show_offline_stations == true">
                                <div class="row" v-if="station.online == true">
                                    <div class="col-auto">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="40">
                                            <circle cx="10" cy="23" r="10" fill="#28a745" />
                                        </svg>
                                    </div>
                                    <div class="col">
                                        <div class="row">
                                            <div class="col">@{{station.callsign}}</div>
                                            <div class="col text-right text-monospace">@{{station.frequency}}</div>
                                        </div>
                                        <small>@{{station.controller}}</small>
                                    </div>
                                </div>
                                <div class="row" v-if="station.online == false">
                                    <div class="col-auto">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="40">
                                            <circle cx="10" cy="23" r="10" fill="#dc3545" />
                                        </svg>
                                    </div>
                                    <div class="col">
                                        <div class="row">
                                            <div class="col text-muted">@{{station.callsign}}</div>
                                            <div class="col text-right text-monospace"></div>
                                        </div>
                                        <small class="text-muted">N/A</small>
                                    </div>
                                </div>         
                            </li>
                        </template>
                    </ul>          
                </div>
                <div class="text-right">
                    <small>Last update: @{{last_update}}</small>
                </div>  
            </div>
        </div>
        <script>
            const StationApp = {
                data() {
                    return {
                        loading: true,
                        stations: [],
                        requested_stations: {!!$stations!!},
                        last_update: null,
                        show_offline_stations: {!!$show_offline_stations!!}
                    }
                },
                methods: {
                    loadData() {
                        axios.get('/api/stations?stations='+this.requested_stations.join(','))
                            .then((response) => {
                                let available_stations = response.data;

                                let stations = this.requested_stations.map((item) => {
                                    let station = available_stations.find((element) => {
                                        return (element.callsign == item);
                                    });

                                    if (typeof station === "undefined") {
                                        return {
                                            callsign: item,
                                            online: false,
                                            frequency: null,
                                            controller: null
                                        }
                                    }

                                    return {
                                        callsign: item,
                                        online: true,
                                        frequency: station.frequency,
                                        controller: station.realname
                                    }
                                });

                                this.stations = stations;
                                this.last_update = moment().format('HH:mm:ss');
                                this.loading = false;
                            });
                    }
                },
                mounted() {
                    this.loadData();
                        
                    setInterval(() => {
                        this.loadData();
                    }, 30000)
                }
            }

            Vue.createApp(StationApp).mount('#mountPoint')
        </script>
    </body>
</html>
