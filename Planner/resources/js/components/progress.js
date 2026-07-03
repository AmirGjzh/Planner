const progressComponent = ({
    max,
    min,
    duration,
    value,
    buffer
}) => {
    return {
        // transport state
        __state: null,

        // internal states
        __value: 0,
        __buffer: null,
        __indeterminate: false,
        __isCompound: false,

        // config 
        max,
        min,
        duration,

        init() {
            this.$nextTick(() => {
                const model = this.$root?._x_model?.get();

                if (this.isCompound(model)) {
                    this.__isCompound = true;
                    this.__value = model.value ?? value;
                    this.__buffer = model.buffer ?? buffer;

                    if (this.isBoolean(model.indeterminate)) {
                        this.__indeterminate = model.indeterminate;
                    }
                } else if (model !== undefined && model !== null) {
                    this.__value = model;
                    this.__buffer = buffer;
                } else {
                    this.__value = value;
                    this.__buffer = buffer;
                }

                this.normalize();
                this.syncState();
            });

            this.$watch('__state', (next) => {

                if (this.__isCompound) {

                    this.__value = next.value;

                    if (next.buffer) {
                        this.__buffer = next.buffer;
                    }

                    if (this.isBoolean(next.indeterminate)) {
                        this.__indeterminate = next.indeterminate;
                    }
                } else {
                    this.__value = next;
                }

                this.normalize();
            });
        },

        isCompound(v) {
            return v && typeof v === 'object' && !Array.isArray(v);
        },

        normalize() {
            // clamp value
            this.__value = Math.min(Math.max(this.__value, this.min), this.max);

            if (this.__buffer === null || this.__buffer === undefined) return;

            // buffer must never go below value
            this.__buffer = Math.max(this.__buffer, this.__value);

            // clamp buffer
            this.__buffer = Math.min(this.__buffer, this.max);

            // trigger the external reactivity
            this.commitBufferChanges();
        },
        
        commitBufferChanges() {
            this.__state.buffer = this.__buffer;
        },
        
        syncState() {
            if (this.__isCompound) {
                let state = { value: undefined, buffer: undefined, indeterminate: undefined };

                if (this.__value !== undefined) state.value = this.__value;

                if (this.__buffer !== undefined) state.buffer = this.__buffer;

                if (this.isBoolean(this.__indeterminate)) state.indeterminate = this.__indeterminate;

                this.__state = state;

            } else {
                this.__state = this.__value;
            }
        },

        isBoolean(val) {
            return val === true || val === false;
        },

        get percentage() {
            if (this.__indeterminate) return 0;
            return ((this.__value - this.min) / (this.max - this.min)) * 100;
        },

        get bufferPercentage() {
            if (this.__indeterminate) return 0;
            if (this.__buffer === null || this.__buffer === undefined) return 0;

            return ((this.__buffer - this.min) / (this.max - this.min)) * 100;
        },

        get displayValue() {
            return Math.round(this.percentage);
        },

        isComplete() {
            return this.__value >= this.max;
        },
    }
}

Alpine.data('progressComponent', progressComponent);
