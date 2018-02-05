<template>
    <div class="container feature-flags-app">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="py-5 text-center">
                    <div class="logo m-auto" title="Deploy often, release when ready.">
                    </div>
                </div>

                <div class="justify-content-between d-flex align-items-center mb-3">
                    <h4>
                        <span class="text-muted">Flags</span>
                    </h4>

                    <a href class="btn btn-outline" @click.prevent="showNewFlagModal = true">
                        New Flag
                    </a>
                </div>


                <ul class="list-group mb-3">
                    <li class="list-group-item text-center lh-condensed py-4" v-if="flags.length === 0">
                        {{ loading ? 'Loading...' : 'No flags registed yet.' }}
                    </li>
                    <li class="list-group-item d-flex justify-content-between lh-condensed" v-for="flag in flags" :key="flag.id">
                        <div>
                            <h6 class="my-0"><strong>{{ flag.flag }}</strong></h6>
                            <small class="text-muted" >{{ flag.description }}</small>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="mx-2 mt-2">
                                <toggle-button
                                    v-model="flag.value"
                                    @change="toggleFlag(flag)"
                                    :sync="true"
                                />
                            </div>

                            <button
                                class="btn btn-sm m-2"
                                title="Create bypass rules."
                                @click="showByPassConfirmationModal(flag)"
                            >
                                <svg width="15" height="15" class="octicon octicon-shield" viewBox="0 0 14 16" version="1.1" aria-hidden="true"><path fill-rule="evenodd" d="M7 0L0 2v6.02C0 12.69 5.31 16 7 16c1.69 0 7-3.31 7-7.98V2L7 0zM5 11l1.14-2.8a.568.568 0 0 0-.25-.59C5.33 7.25 5 6.66 5 6c0-1.09.89-2 1.98-2C8.06 4 9 4.91 9 6c0 .66-.33 1.25-.89 1.61-.19.13-.3.36-.25.59L9 11H5z"></path></svg>
                            </button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <b-modal
                :visible="bypassing !== null"
                @hidden="hideByPassingModal"
                @ok="hideByPassingModal"
                :ok-only="true"
                ok-variant="default"
        >
            <span slot="modal-ok">Done</span>
            <span slot="modal-title">
                Register bypass rules
            </span>
            <div v-if="bypassing">
                <p class="alert alert-info">
                    <strong>Attention!</strong> Users that passes the bypass rule will have access to the feature.
                </p>

                <h4 class="mb-3">Allowed IDs</h4>
                <div class="row">
                    <div class="container">
                        <div class="form-row align-items-center">
                            <div class="col-sm-4 my-1">
                                <label class="sr-only" >Allow ID</label>
                                <input type="text" class="form-control" v-model="newByPassId" placeholder="Example: 123" />
                            </div>
                            <div class="col-auto my-1">
                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    :disabled="bypass_saving"
                                    @click="() => addByPassId()"
                                >
                                    Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <p class="text-muted">The IDs listed bellow all have access to this feature.</p>

                        <ul class="list-group">
                            <li v-if="!bypassing.bypass_ids || bypassing.bypass_ids.length === 0" class="list-group-item text-center">
                                No allowed id yet.
                            </li>
                            <li
                                v-if="bypassing.bypass_ids"
                                class="list-group-item justify-content-between d-flex"
                                v-for="id in bypassing.bypass_ids"
                                :key="id"
                            >
                                <span>ID: {{ id }}</span>

                                <button
                                    class="btn btn-sm"
                                    :disabled="bypass_saving"
                                    @click="removeByPassId(id)"
                                >
                                    Remove
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                </div>
        </b-modal>
        <b-modal
            v-model="showNewFlagModal"
            cancel-variant="default"
            @ok="createFeatureFlag"
        >
            <span slot="modal-ok">Save</span>
            <span slot="modal-cancel">Close</span>
            <span slot="modal-title">
                New Feature Flag
            </span>
            <div>
                <form>
                    <div class="form-group">
                        <label for="flag">Flag</label>
                        <input type="text" class="form-control" id="flag" :value="newFlag.flag" @input="(e) => newFlag.flag = e.target.value.toUpperCase()" aria-describedby="flagHelp" placeholder="EXAMPLE_FLAG" />
                    </div>
                    <div class="form-group">
                        <label for="shortDescription">Short Description</label>
                        <input type="text" v-model="newFlag.description" class="form-control" id="shortDescription" placeholder="Lorem ipsum." />
                    </div>
                    <div class="form-group">
                        <div class="">
                            <toggle-button
                                v-model="newFlag.value"
                            />
                            <span class="ml-2">
                                {{ newFlag.value ? 'Start enabled' : 'Start disabled' }}
                            </span>
                        </div>
                    </div>
                </form>
            </div>
        </b-modal>
    </div>
</template>

<script>
    import swal from 'sweetalert';

    export default {
        data () {
            return {
                showToggleConfirmationModal: false,
                showNewFlagModal: false,
                toggling: null,
                loading: false,
                flags: [],
                bypassing: null,
                bypass_saving: false,
                newByPassId: '',
                newFlag: {
                    flag: '',
                    description: '',
                    value: false,
                    bypass: false,
                    rules: []
                },
            };
        },
        methods: {
            toggleFlag (flag) {
                swal({
                    title: "Are you sure?",
                    text: "As this might affect users of your application, are you sure you want to do this?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willToggle) => {
                        if (willToggle) {
                            this.sendToggleRequest(flag)
                                .catch(() => {
                                    flag.value = !flag.value;
                                    swal("Whoops! For some reason, we could not toggle the feature.");
                                })
                        } else {
                            flag.value = !flag.value;
                        }
                    });
            },
            sendToggleRequest(flag) {
                // The toggle button changes the value before it calls the on-change handler.
                if (!flag.value) {
                    return axios.post('/feature-flags/disabled-flags', {
                        feature_flag_id: flag.id,
                        confirmation: 1
                    });
                }

                return axios.post('/feature-flags/enabled-flags', {
                    feature_flag_id: flag.id,
                    confirmation: 1,
                });
            },
            showByPassConfirmationModal (flag) {
                this.bypassing = flag;
            },
            addByPassId () {
                if (!this.bypassing.bypass_ids) {
                    this.bypassing.bypass_ids = [];
                }

                this.bypassing.bypass_ids.push(this.newByPassId);

                this.saveByPasses()
                    .then(() => {
                        this.newByPassId = '';
                    });
            },
            removeByPassId(id) {
                // In case the request fails, we need the old list.
                let oldList = this.bypassing.bypass_ids;

                this.bypassing.bypass_ids = this.bypassing.bypass_ids.filter((byPassId) => byPassId !== id);

                this.saveByPasses()
                    .catch(() => {
                        this.bypassing.bypass_ids = oldList;
                    });
            },
            saveByPasses () {
                this.bypass_saving = true;

                return axios
                    .put('/feature-flags/flags/' + this.bypassing.id, {
                        bypass_ids: this.bypassing.bypass_ids,
                    })
                    .then(({data}) => {
                        this.bypassing = data;
                        this.updateFlagInList(data);
                        this.bypass_saving = false;

                        return data;
                    })
                    .catch((error) => {
                        this.bypass_saving = false;
                        swal("Whoops! For some reason, we could not add the rule.");
                        throw error;
                    });
            },
            updateFlagInList (flag) {
                let index = this.flags.findIndex((f) => f.id === flag.id);

                if (index !== -1) {
                    this.flags.splice(index, 1, flag);
                }
            },
            hideByPassingModal () {
                this.bypassing = null;
                this.bypass_saving = false;
            },
            createFeatureFlag () {
                axios.post('/feature-flags/flags', this.newFlag)
                    .then(({data}) => {
                        this.flags.push(data);
                    });
            }
        },
        mounted() {
            this.loading = true;

            axios.get('/feature-flags/flags')
                .then(({data}) => {
                    this.flags = data;
                    this.loading = false;
                });
        }
    }
</script>

<style scoped>
    .feature-flags-app {
        padding-top: 10px;
    }
</style>