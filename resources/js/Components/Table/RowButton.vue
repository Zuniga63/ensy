<template>
  <Link v-if="href" :href="href" :class="classes" v-bind="$attrs">
    <show-icon v-if="type === 'show'" :solid="solid" />
    <edit-icon v-if="type === 'edit'" :solid="solid" />
    <delete-icon v-if="type === 'delete'" :solid="solid" />
  </Link>

  <a
    href="javascript:;"
    v-else
    :class="classes"
    @click="$emit('click')"
    v-bind="$attrs"
  >
    <show-icon v-if="type === 'show'" :solid="solid" />
    <edit-icon v-if="type === 'edit'" :solid="solid" />
    <delete-icon v-if="type === 'delete'" :solid="solid" />
  </a>

</template>
<script>
import { Link } from "@inertiajs/inertia-vue3";
import ShowIcon from "@/Components/Svgs/ClipboardList.vue";
import EditIcon from "@/Components/Svgs/Edit.vue";
import DeleteIcon from "@/Components/Svgs/Trash.vue";

export default {
  components: {
    Link,
    ShowIcon,
    EditIcon,
    DeleteIcon,
  },
  emits: ["click"],
  props: {
    href: String,
    type: {
      type: String,
      default: "edit",
    },
    solid: Boolean,
  },
  data() {
    return {};
  },
  computed: {
    classes() {
      let base = [
        "p-2",
        "border",
        "rounded",
        "transition-colors",
        "hover:ring",
        "hover:ring-opacity-40",
      ];

      let customClass = [];

      if (this.type === "edit") {
        customClass = [
          "border-green-400",
          "text-green-500",
          "hover:bg-green-100",
          "hover:ring-green-400",
        ];
      } else if (this.type === "show") {
        customClass = [
          "border-gray-400",
          "text-gray-800",
          "hover:bg-gray-100",
          "hover:ring-gray-500",
        ];
      } else if (this.type === "delete") {
        customClass = [
          "border-red-400",
          "text-red-500",
          "hover:bg-red-100",
          "hover:ring-red-400",
        ];
      }

      base.push(...customClass);

      return base;
    },
  },
};
</script>