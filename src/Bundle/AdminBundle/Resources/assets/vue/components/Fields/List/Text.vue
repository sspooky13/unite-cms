<template>
  <div>
    <template v-for="value in values">
      {{ value }}
      <br v-if="!isLastValue(value)" />
    </template>
  </div>
</template>
<script>
  import _abstract from "./_abstract";
  export default {
      extends: _abstract
  }
</script>
