
import Vue from 'vue';
import { getIntrospectionQuery } from 'graphql'
import gql from 'graphql-tag';
import User from './User';

export const ContentTypes = new Vue({
    data() {
        return {
            contentTypes: [],
            userTypes: [],
            singleContentTypes: [],
            embeddedContentTypes: [],
        }
    },
    created() {
        this.$on('load', (data, success, fail, fin) => {

            if(!User.isAuthenticated) {
                if(success) { success(); }
                return;
            }

            this.$apollo.query({
                query: gql(getIntrospectionQuery()),
            }).then((data) => {

                this.contentTypes = [];
                this.userTypes = [];
                this.singleContentTypes = [];
                this.embeddedContentTypes = [];

                data.data.__schema.types.forEach((type) => {

                    if(!type.interfaces) {
                        return;
                    }

                    type.interfaces.forEach((interf) => {
                        switch (interf.name) {
                            case 'UniteContent':
                                this.contentTypes.push(this.transformType(type));
                                break;
                            case 'UniteUser':
                                this.userTypes.push(this.transformType(type));
                                break;
                            case 'UniteSingleContent':
                                this.singleContentTypes.push(this.transformType(type));
                                break;
                            case 'UniteEmbeddedContent':
                                this.embeddedContentTypes.push(this.transformType(type));
                                break;
                        }
                    });

                });
            }).catch(fail).catch(fin).then(success);
        });

        // Reload unite types on user login / logout
        User.$watch('token', () => {
            this.$emit('load');
        });
    },
    methods: {
        transformType(type) {
            return {
                id: type.name,
                name: type.name,
                description: type.description
            };
        }
    }
});
export default ContentTypes;