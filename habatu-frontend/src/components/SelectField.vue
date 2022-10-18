<template>
	<div class="relative w-full">
		<label class="mb-1 block text-sm font-bold text-gray-700" for="username">
			{{ label }}
		</label>
		<input
			autocomplete="off"
			v-model="textValue"
			@blur="handleBlur"
			class="focus:shadow-outline w-full appearance-none rounded border py-2 px-3 leading-tight text-gray-700 focus:outline-none focus:ring-1 focus:ring-gray-400"
			:id="label"
			:type="type"
			:placeholder="label" />
		<div
			id="dropdown"
			v-if="select.length > 0"
			class="absolute left-0 right-0 z-50 rounded-b-lg bg-gray-100 p-3 text-left">
			<button
				id="dropdown"
				@click="handleSelect(item.name)"
				v-for="item in select"
				class="w-full cursor-pointer rounded-lg bg-gray-100 p-3 hover:bg-gray-300"
				:key="item.name">
				{{ item.name }}
			</button>
		</div>
	</div>
</template>

<script>
export default {
	props: ["label", "type", "modelValue", "options"],
	emits: ["update:modelValue"],
	data() {
		return {
			select: [],
			selectKey: 0,
			textValue: undefined,
		}
	},
	methods: {
		handleSelect(name) {
			this.$emit(
				"update:modelValue",
				this.options.find(o => o.name == name)._id
			)
			this.textValue = name
			this.select = []
		},
		handleBlur(evt) {
			if (evt.relatedTarget && evt.relatedTarget.id == "dropdown") {
				return
			}
			this.select = []
		},
	},
	watch: {
		modelValue(newVal) {
			const value = this.options.find(o => o._id == newVal)
			if (value) {
				this.textValue = value.name
			}
		},
		textValue(newVal) {
			if (this.options.map(o => o.name).indexOf(newVal) > -1) {
				this.$emit(
					"update:modelValue",
					this.options.find(o => o.name == newVal)._id
				)
				this.select = []
				return
			}
			this.$emit("update:modelValue", undefined)
			this.select = this.options.filter(o =>
				o.name.toLowerCase().includes(newVal.toLowerCase())
			)
			console.log(this.select[0])
			this.selectKey++
		},
	},
	mounted() {
		if (this.modelValue && typeof this.modelValue != "string") {
			if (this.modelValue._id) {
				this.$emit("update:modelValue", this.modelValue._id)
			}
		}
	},
}
</script>

<style></style>
