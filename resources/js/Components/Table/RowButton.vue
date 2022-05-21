<template>
  <Link v-if="href" :href="href" :class="classes" v-bind="$attrs">
    <folder-open-icon v-if="type === 'show'" :solid="solid" class="h-5 w-5" />
    <edit-icon v-else-if="type === 'edit'" :solid="solid" class="h-5 w-5" />
    <delete-icon v-else-if="type === 'delete'" :solid="solid" class="h-5 w-5" />
  </Link>

  <a href="javascript:;" v-else :class="classes" @click="$emit('click')" v-bind="$attrs">
    <folder-open-icon v-if="type === 'show'" :solid="solid" class="h-5 w-5" />
    <edit-icon v-else-if="type === 'edit'" :solid="solid" class="h-5 w-5" />
    <delete-icon v-else-if="type === 'delete'" :solid="solid" class="h-5 w-5" />
  </a>
</template>
<script>
import { Link } from "@inertiajs/inertia-vue3";
import FolderOpenIcon from "@/Components/Svgs/FolderOpen.vue";
import EditIcon from "@/Components/Svgs/Edit.vue";
import DeleteIcon from "@/Components/Svgs/Trash.vue";

export default {
  components: {
    Link,
    FolderOpenIcon,
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
      let base = ["p-1", "border", "rounded", "transition-colors", "hover:ring", "hover:ring-opacity-40"];

      let customClass = [];

      if (this.type === "edit") {
        customClass = ["border-green-400", "text-green-500", "hover:bg-green-100", "hover:ring-green-400"];
      } else if (this.type === "show") {
        customClass = ["border-gray-400", "text-gray-800", "hover:bg-gray-100", "hover:ring-gray-500"];
      } else if (this.type === "delete") {
        customClass = ["border-red-400", "text-red-500", "hover:bg-red-100", "hover:ring-red-400"];
      }

      base.push(...customClass);

      return base;
    },
  },
};
</script>