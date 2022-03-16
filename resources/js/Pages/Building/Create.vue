<template>
  <app-layout title="Registrar Inmueble">
    <template #header>
      <div class="flex justify-between w-full">
        <!-- TITLE OF HEADER -->
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Registro de Inmueble
        </h2>

        <!-- BUTTON FOR ADD CUSTOMER -->
        <link-button :href="route('building.index')"> Regresar </link-button>
      </div>
    </template>

    <!-- Body -->
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
      <jet-form-section @submitted="submit">
        <template #title> Formulario de Registro </template>
        <template #description>
          Permite registrar la información de un inmueble.
        </template>

        <template #form>
          <!-- Información General -->
          <input-group-section title="Información general" class="col-span-6">
            <div class="grid grid-cols-2 gap-6">
              <!-- Imagen, descripción y disponibilidad-->
              <div class="col-span-2 lg:col-span-1">
                <div class="flex flex-col items-center">
                  <!-- Image -->
                  <div
                    class="
                      relative
                      w-60
                      h-60
                      mb-2
                      mt-1
                      ring ring-gray-500
                      rounded-sm
                      overflow-hidden
                      hover:cursor-pointer
                    "
                    @click="$refs.image.click()"
                  >
                    <!-- Cuando no hay imagen -->
                    <transition
                      enter-active-class="transition ease-out duration-200"
                      enter-from-class="opacity-0 scale-90"
                      enter-to-class="opacity-100 scale-100"
                      leave-active-class="transition ease-in duration-200"
                      leave-from-class="opacity-100 scale-100"
                      leave-to-class="opacity-0 scale-90"
                    >
                      <div
                        class="
                          w-full
                          h-full
                          flex
                          items-center
                          justify-center
                          text-gray-600
                          bg-gray-100
                        "
                        v-show="!imagePreview"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-40 w-40"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                          stroke-width="2"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                          />
                        </svg>
                      </div>
                    </transition>

                    <!-- Cuando la imagen ha sido cargada -->
                    <transition
                      enter-active-class="transition ease-out duration-200"
                      enter-from-class="opacity-0 scale-90"
                      enter-to-class="opacity-100 scale-100"
                      leave-active-class="transition ease-in duration-200"
                      leave-from-class="opacity-100 scale-100"
                      leave-to-class="opacity-0 scale-90"
                    >
                      <img
                        v-show="imagePreview"
                        :src="imagePreview"
                        alt="Imagen del inmueble"
                        class="block w-full h-full object-cover object-center"
                      />
                    </transition>

                    <!-- Boton para Quitar -->
                    <a
                      href="javascript:;"
                      class="
                        absolute
                        top-2
                        right-2
                        p-1
                        rounded-full
                        border border-red-200
                        bg-white
                        text-red-500
                        hover:border-red-500 hover:bg-red-50
                        transition-colors
                        z-10
                      "
                      title="Remover Imagen"
                      v-show="imagePreview"
                      @click.stop="removeImagePreview"
                    >
                      <trash-icon />
                    </a>
                  </div>

                  <!-- Input -->
                  <input
                    type="file"
                    class="hidden"
                    ref="image"
                    @change="updateImagePreview"
                    accept="image/*"
                  />

                  <jet-input-error :message="form.errors.image" class="mt-2" />

                  <!-- upload button -->
                  <jet-secondary-button
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    type="button"
                    @click="$refs.image.click()"
                  >
                    <div class="flex items-center">
                      <!-- Heroicon:cloud-upload -->
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-2"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"
                        />
                        <path d="M9 13h2v5a1 1 0 11-2 0v-5z" />
                      </svg>

                      <span>Subir Imagen</span>
                    </div>
                  </jet-secondary-button>

                  <!-- Description -->
                  <div class="self-stretch mt-2">
                    <custom-label
                      for="building-description"
                      value="Descripción"
                      class="mb-2"
                    />

                    <textarea
                      name="building-description"
                      id="building-description"
                      cols="30"
                      rows="5"
                      class="
                        block
                        w-full
                        border border-gray-200
                        rounded
                        text-sm text-gray-800
                        focus:ring focus:ring-indigo-400 focus:ring-opacity-40
                      "
                      placeholder="Escribe aquí una descripción del inmueble."
                      v-model="form.description"
                      ref="description"
                    ></textarea>

                    <!-- Error -->
                    <jet-input-error
                      :message="form.errors.description"
                      class="mt-2"
                    />
                  </div>
                </div>
              </div>

              <!-- Información general -->
              <div class="col-span-2 lg:col-span-1">
                <div class="grid grid-cols-2 gap-2">
                  <!-- Habitaciones -->
                  <div>
                    <!-- Etiqueta -->
                    <custom-label
                      for="building-rooms"
                      class="mb-2"
                      value="Habitaciones"
                    />
                    <!-- Input -->
                    <jet-input
                      id="building-rooms"
                      name="building-rooms"
                      type="number"
                      v-model="form.rooms"
                      class="w-full text-center text-sm"
                      placeholder="# Hab."
                      min="1"
                      max="100"
                    />

                    <!-- Error -->
                    <jet-input-error
                      :message="form.errors.rooms"
                      class="mt-2"
                    />
                  </div>

                  <!-- Baños -->
                  <div>
                    <!-- Etiqueta -->
                    <custom-label
                      for="building-bathrooms"
                      class="mb-2"
                      value="Baños"
                    />
                    <!-- Input -->
                    <jet-input
                      id="building-bathrooms"
                      name="building-bathrooms"
                      type="number"
                      v-model="form.bathrooms"
                      class="w-full text-center text-sm"
                      placeholder="# Baños"
                      min="1"
                      max="100"
                    />

                    <!-- Error -->
                    <jet-input-error
                      :message="form.errors.bathrooms"
                      class="mt-2"
                    />
                  </div>

                  <!-- Paqueaderos -->
                  <div>
                    <!-- Etiqueta -->
                    <custom-label
                      for="building-parking-lots"
                      class="mb-2"
                      value="Parqueaderos"
                    />
                    <!-- Input -->
                    <jet-input
                      id="building-parking-lots"
                      name="building-parking-lots"
                      type="number"
                      v-model="form.parking_lots"
                      class="w-full text-center text-sm"
                      placeholder="# parking"
                      min="1"
                      max="100"
                    />

                    <!-- Error -->
                    <jet-input-error
                      :message="form.errors.parking_lots"
                      class="mt-2"
                    />
                  </div>

                  <!-- Estrato -->
                  <div>
                    <!-- Etiqueta -->
                    <custom-label
                      for="building-socioeconomics"
                      class="mb-2"
                      value="Estrato"
                    />
                    <!-- Input -->
                    <jet-input
                      id="building-socioeconomics"
                      name="building-socioeconomics"
                      type="number"
                      v-model="form.socioeconomic"
                      class="w-full text-center text-sm"
                      placeholder="# estrato"
                      min="1"
                      max="100"
                    />

                    <!-- Error -->
                    <jet-input-error
                      :message="form.errors.socioeconomic"
                      class="mt-2"
                    />
                  </div>

                  <!-- Area total -->
                  <div>
                    <!-- Etiqueta -->
                    <custom-label
                      for="building-area"
                      class="mb-2"
                      value="Área Total [m2]"
                    />
                    <!-- Input -->
                    <jet-input
                      id="building-area"
                      name="building-area"
                      type="number"
                      v-model="form.area"
                      class="w-full text-center text-sm"
                      placeholder="área"
                      min="1"
                      step="0.1"
                    />

                    <!-- Error -->
                    <jet-input-error :message="form.errors.area" class="mt-2" />
                  </div>

                  <!-- Area privada -->
                  <div>
                    <!-- Etiqueta -->
                    <custom-label
                      for="building-private-area"
                      class="mb-2"
                      value="Area Privada [m2]"
                    />
                    <!-- Input -->
                    <jet-input
                      id="building-private-area"
                      name="building-private-area"
                      type="number"
                      v-model="form.private_area"
                      class="w-full text-center text-sm"
                      placeholder="área"
                      min="1"
                      step="0.1"
                    />

                    <!-- Error -->
                    <jet-input-error
                      :message="form.errors.private_area"
                      class="mt-2"
                    />
                  </div>

                  <!-- Piso -->
                  <div>
                    <!-- Etiqueta -->
                    <custom-label
                      for="building-floor"
                      class="mb-2"
                      value="Piso"
                    />
                    <!-- Input -->
                    <jet-input
                      id="building-floor"
                      name="building-floor"
                      type="number"
                      v-model="form.floor"
                      class="w-full text-center text-sm"
                      placeholder="# piso"
                      min="1"
                      max="100"
                    />

                    <!-- Error -->
                    <jet-input-error
                      :message="form.errors.floor"
                      class="mt-2"
                    />
                  </div>

                  <!-- Antiguedad -->
                  <div>
                    <!-- Etiqueta -->
                    <custom-label for="building-antiquity" class="mb-2">
                      Antiguedad
                      <span class="text-xs text-gray-400" v-if="antiquity"
                        >[{{ antiquity }}]</span
                      >
                    </custom-label>
                    <!-- Input -->
                    <jet-input
                      id="building-antiquity"
                      name="building-antiquity"
                      type="date"
                      v-model="form.antiquity"
                      class="w-full text-center text-sm"
                      placeholder="Fecha"
                      :max="maxDate"
                    />

                    <!-- Error -->
                    <jet-input-error
                      :message="form.errors.antiquity"
                      class="mt-2"
                    />
                  </div>

                  <!-- Campo para otras caracteristicas -->
                  <div class="col-span-2">
                    <custom-label
                      for="building-pfeatures"
                      class="mb-2"
                      value="Mas Caracteristicas"
                    />

                    <div class="grid grid-cols-6 gap-1 items-center mb-2">
                      <jet-input
                        id="building-features"
                        name="building-pfeatures"
                        type="text"
                        v-model.trim="feature"
                        class="col-span-5 w-full text-center"
                        placeholder="Escribela aquí."
                        @keydown.prevent.enter="addFeature"
                      />

                      <div class="flex items-center justify-center">
                        <a
                          href="javascript:;"
                          class="
                            p-2
                            border border-gray-200
                            rounded-full
                            text-gray-600
                            hover:bg-green-400
                            hover:border-green-500
                            hover:text-gray-50
                            transition-colors
                            shadow
                          "
                          @click="addFeature"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M12 4v16m8-8H4"
                            />
                          </svg>
                        </a>
                      </div>
                    </div>

                    <!-- Listado de caracteristicas -->
                    <div
                      class="
                        flex flex-wrap
                        items-start
                        border
                        p-2
                        border-gray-200
                        rounded-sm
                        h-20
                        overflow-y-auto
                      "
                    >
                      <div
                        v-for="(item, index) in features"
                        :key="index"
                        class="
                          px-2
                          py-1
                          mr-2
                          mb-1
                          border border-gray-200
                          rounded
                          text-xs text-gray-600
                          hover:cursor-pointer
                          hover:bg-red-50
                          hover:border-red-400
                          hover:text-red-500
                          transition-colors
                          select-none
                        "
                        @click="removeFeature(index)"
                      >
                        {{ item }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </input-group-section>

          <!-- Dirección del Inmueble -->
          <input-group-section
            class="col-sapn-6"
            title="Dirección del Inmueble"
          >
            <div class="grid grid-cols-2 gap-4">
              <!-- Grupo de Administración -->
              <div class="col-span-2 lg:col-span-1">
                <!-- Etiqueta -->
                <custom-label
                  for="buildign-admin"
                  class="mb-2"
                  value="Administración"
                />

                <!-- Campo -->
                <select
                  name="buildign-admin"
                  id="buildign-admin"
                  v-model="form.building_admin_id"
                  class="
                    w-full
                    px-6
                    py-2
                    border border-gray-300
                    focus:border-indigo-300
                    focus:ring
                    focus:ring-indigo-200
                    focus:ring-opacity-50
                    rounded-md
                    shadow-sm
                    text-sm text-gray-800
                  "
                >
                  <option :value="null">No tiene administración</option>
                  <option
                    v-for="admin in admins"
                    :key="admin.id"
                    :value="admin.id"
                  >
                    {{ admin.name }}
                  </option>
                </select>

                <!-- Mensaje de error -->
                <jet-input-error
                  :message="form.errors.building_admin_id"
                  class="mt-2"
                />
              </div>

              <!-- Tipo de inmueble y otros-->
              <div class="col-span-2 lg:col-span-1">
                <div class="grid grid-cols-3 gap-2">
                  <!-- Selector del tipo -->
                  <div class="col-span-2">
                    <!-- Etiqueta -->
                    <custom-label
                      for="buildign-type"
                      class="mb-2"
                      value="Tipo de Inmueble"
                    />

                    <!-- Campo -->
                    <select
                      name="buildign-type"
                      id="buildign-type"
                      v-model="form.building_type"
                      class="
                        w-full
                        px-6
                        py-2
                        border border-gray-300
                        focus:border-indigo-300
                        focus:ring
                        focus:ring-indigo-200
                        focus:ring-opacity-50
                        rounded-md
                        shadow-sm
                        text-sm text-gray-800
                      "
                    >
                      <option :value="null">Indefinido</option>
                      <option value="house">Casa</option>
                      <option value="apartment">Apartamento</option>
                      <option value="business">Local</option>
                    </select>

                    <!-- Mensaje de error -->
                    <jet-input-error
                      :message="form.errors.building_type"
                      class="mt-2"
                    />
                  </div>

                  <!-- Numero del local o apartamento -->
                  <div>
                    <!-- Etiqueta -->
                    <custom-label
                      for="building-address-apartment"
                      class="mb-2 transition-opacity duration-300"
                      :class="{
                        'text-opacity-20': !additionalAddress,
                      }"
                    >
                      <span v-if="form.building_type === 'business'"
                        >Local</span
                      >
                      <span v-else>Apartamento</span>
                    </custom-label>
                    <!-- Input -->
                    <jet-input
                      id="building-address-apartment"
                      name="building-address-apartment"
                      type="number"
                      v-model="address.apartment"
                      class="w-full text-center text-sm text-gray-800"
                      :class="{
                        'border-opacity-40': !additionalAddress,
                        'text-opacity-40': !additionalAddress,
                      }"
                      placeholder="Ej: 401"
                      min="1"
                      :disabled="!additionalAddress"
                    />

                    <!-- Error -->
                    <jet-input-error
                      :message="form.errors.socioeconomic"
                      class="mt-2"
                    />
                  </div>
                </div>
              </div>

              <!-- Departamento -->
              <div class="col-span-2 lg:col-span-1">
                <!-- Etiqueta -->
                <custom-label
                  for="buildign-department"
                  class="mb-2"
                  value="Departamento"
                  required
                />

                <!-- Campo -->
                <select
                  name="buildign-department"
                  id="buildign-department"
                  v-model="form.country_department_id"
                  class="
                    w-full
                    px-6
                    py-2
                    border border-gray-300
                    focus:border-indigo-300
                    focus:ring
                    focus:ring-indigo-200
                    focus:ring-opacity-50
                    rounded-md
                    shadow-sm
                    text-sm text-gray-800
                  "
                >
                  <option :value="null">Selecciona un Departamento</option>
                  <option
                    v-for="department in departments"
                    :key="department.id"
                    :value="department.id"
                  >
                    {{ department.name }} ({{ department.towns.length }}
                    municipios)
                  </option>
                </select>

                <!-- Mensaje de error -->
                <jet-input-error
                  :message="form.errors.country_department_id"
                  class="mt-2"
                />
              </div>

              <!-- Municipio -->
              <div class="col-span-2 lg:col-span-1">
                <!-- Etiqueta -->
                <custom-label
                  for="buildign-town"
                  class="mb-2"
                  value="Municipio"
                  required
                />

                <!-- Campo -->
                <select
                  name="buildign-town"
                  id="buildign-town"
                  v-model="form.town_id"
                  class="
                    w-full
                    px-6
                    py-2
                    border border-gray-300
                    focus:border-indigo-300
                    focus:ring
                    focus:ring-indigo-200
                    focus:ring-opacity-50
                    rounded-md
                    shadow-sm
                    text-sm text-gray-800
                  "
                >
                  <option :value="null">Selecciona un Municipio</option>
                  <option v-for="town in towns" :key="town.id" :value="town.id">
                    {{ town.name }} ({{ town.districts.length }}
                    barrios)
                  </option>
                </select>

                <!-- Mensaje de error -->
                <jet-input-error :message="form.errors.town_id" class="mt-2" />
              </div>

              <!-- Barrio -->
              <div class="col-span-2 lg:col-span-1">
                <!-- Etiqueta -->
                <custom-label
                  for="buildign-district"
                  class="mb-2"
                  value="Barrio"
                  required
                />

                <!-- Campo -->
                <select
                  name="buildign-district"
                  id="buildign-district"
                  v-model="form.town_district_id"
                  class="
                    w-full
                    px-6
                    py-2
                    border border-gray-300
                    focus:border-indigo-300
                    focus:ring
                    focus:ring-indigo-200
                    focus:ring-opacity-50
                    rounded-md
                    shadow-sm
                    text-sm text-gray-800
                  "
                >
                  <option :value="null">Selecciona un Barrio</option>
                  <option
                    v-for="district in districts"
                    :key="district.id"
                    :value="district.id"
                  >
                    {{ district.name }} ({{ district.town.name }})
                  </option>
                </select>

                <!-- Mensaje de error -->
                <jet-input-error
                  :message="form.errors.town_district_id"
                  class="mt-2"
                />
              </div>

              <!-- Dirección -->
              <div class="col-span-2 lg:col-span-1">
                <!-- Etiqueta -->
                <custom-label
                  for="building-address"
                  class="mb-2"
                  value="Dirección"
                  required
                />

                <!-- Campo -->
                <jet-input
                  type="text"
                  id="building-address"
                  class="w-full text-sm"
                  placeholder="Ejemplo: Calle 5 #24-34"
                  v-model="address.address"
                />

                <!-- Mensaje de error -->
                <jet-input-error :message="form.errors.address" class="mt-2" />
              </div>

              <!-- Obsrvación -->
              <div class="col-span-2">
                <!-- Etiqueta -->
                <custom-label
                  for="building-observation"
                  class="mb-2"
                  value="Observación"
                />

                <!-- Campo -->
                <jet-input
                  type="text"
                  id="building-observation"
                  class="w-full"
                  placeholder="Ejemplo: Casa de color verde limón con tejado de zinc."
                  v-model="address.observation"
                />

                <!-- Mensaje de error -->
                <jet-input-error
                  :message="form.errors.observation"
                  class="mt-2"
                />
              </div>
            </div>
          </input-group-section>

          <!-- Estado del inmueble -->
          <input-group-section title="Estado del Inmueble">
            <div class="grid grid-cols-2 items-end gap-6 p-2">
              <!-- Propietario -->
              <div class="col-span-2 lg:col-span-1">
                <!-- Etiqueta -->
                <custom-label
                  for="buildign-owner"
                  class="mb-2"
                  value="Propietario"
                />

                <!-- Campo -->
                <select
                  name="buildign-owner"
                  id="buildign-owner"
                  v-model="form.owner_id"
                  class="
                    w-full
                    px-6
                    py-2
                    border border-gray-300
                    focus:border-indigo-300
                    focus:ring
                    focus:ring-indigo-200
                    focus:ring-opacity-50
                    rounded-md
                    shadow-sm
                    text-sm text-gray-800
                  "
                >
                  <option :value="null">Selecciona un Propietario</option>
                  <option
                    v-for="customer in customers"
                    :key="customer.id"
                    :value="customer.id"
                  >
                    {{ customer.full_name }}
                  </option>
                </select>

                <!-- Mensaje de error -->
                <jet-input-error :message="form.errors.owner_id" class="mt-2" />
              </div>

              <sumary-info
                :owner="owner"
                :lease-fee="form.lease_fee"
                :commission="form.commission"
                :insured="form.insured"
                :insurance-commission="form.insurance_commission"
              />

              <!-- Canon de arrendamiento  y comission-->
              <div class="col-span-2">
                <div class="grid grid-cols-8 gap-2 items-center">
                  <!-- Canon de arrendamiento -->
                  <div class="col-span-3">
                    <custom-label
                      for="building-lease-fee"
                      class="inline-block mb-1 text-sm text-gray-800"
                      required
                      value="Canon"
                    />

                    <CurrencyInput
                      name="building-lease-fee"
                      id="building-lease-fee"
                      type="text"
                      class="w-full text-sm text-gray-800 text-right px-4"
                      v-model="form.lease_fee"
                      placeholder="Ingresa la cuota de arrendamiento"
                      autocomplete="off"
                    />

                    <jet-input-error
                      :message="form.errors.lease_fee"
                      class="col-span-12 mt-2"
                    />
                  </div>
                  <!-- Cuota de administración -->
                  <div class="col-span-3">
                    <custom-label
                      for="building-admin-fee"
                      class="mb-2"
                      :class="{
                        'text-opacity-40': !hasAdmin,
                      }"
                      :required="hasAdmin"
                      value="Administración"
                    />

                    <CurrencyInput
                      name="building-admin-fee"
                      id="building-admin-fee"
                      type="text"
                      class="w-full text-sm text-gray-800 text-right px-4"
                      :class="{ 'text-opacity-40': !hasAdmin }"
                      v-model="form.admin_fee"
                      placeholder="Ingresa la cuota de adminsitración."
                      autocomplete="off"
                      :disabled="!hasAdmin"
                    />

                    <jet-input-error
                      :message="form.errors.admin_fee"
                      class="col-span-12 mt-2"
                    />
                  </div>

                  <!-- Comision -->
                  <div class="col-span-2">
                    <!-- Etiqueta -->
                    <custom-label
                      for="building-commission"
                      class="mb-1"
                      value="Comisión [%]"
                      required
                    />
                    <!-- Input -->
                    <jet-input
                      id="building-commission"
                      name="building-commission"
                      type="number"
                      v-model.number="form.commission"
                      class="w-full text-center text-sm"
                      placeholder="0%"
                      min="0"
                      max="100"
                    />

                    <!-- Error -->
                    <jet-input-error
                      :message="form.errors.commission"
                      class="mt-2"
                    />
                  </div>
                </div>
              </div>

              <!-- Checker for insured -->
              <div class="col-span-2">
                <div class="flex">
                  <!-- Has mandate contract -->
                  <label class="flex items-center mr-3">
                    <jet-checkbox
                      name="insured"
                      v-model:checked="form.insured"
                    />
                    <span class="ml-2 text-sm text-gray-600"
                      >¿Está Asegurado?</span
                    >
                  </label>
                </div>
              </div>

              <!-- Aseguradora -->
              <div class="col-span-2 lg:col-span-1">
                <!-- Etiqueta -->
                <custom-label
                  for="building-insurance-carrier"
                  class="mb-2"
                  :class="{
                    'text-opacity-50': !form.insured,
                  }"
                  value="Aseguradora"
                />

                <!-- Campo -->
                <jet-input
                  type="text"
                  id="building-insurance-carrier"
                  class="w-full text-gray-800 transition-opacity"
                  placeholder="Escribe el nombre aquí."
                  v-model="form.insurance_carrier"
                  :class="{
                    'text-opacity-50': !form.insured,
                  }"
                  :disabled="!form.insured"
                />

                <!-- Mensaje de error -->
                <jet-input-error
                  :message="form.errors.insurance_carrier"
                  class="mt-2"
                />
              </div>

              <!-- Tipo de seguro y COmisión -->
              <div class="col-span-2 lg:col-span-1">
                <div class="grid grid-cols-3 gap-2">
                  <!-- Tipo de seguro -->
                  <div class="col-span-2">
                    <!-- Etiqueta -->
                    <custom-label
                      for="building-insurance-type"
                      class="mb-2 text-gray-800"
                      value="Tipo de seguro"
                      :class="{
                        'text-opacity-50': !form.insured,
                      }"
                    />

                    <!-- Campo -->
                    <jet-input
                      type="text"
                      id="building-insurance-type"
                      class="w-full text-gray-800"
                      placeholder="Escribe el tipo de seguro."
                      v-model="form.insurance_type"
                      :class="{
                        'text-opacity-50': !form.insured,
                      }"
                      :disabled="!form.insured"
                    />

                    <!-- Mensaje de error -->
                    <jet-input-error
                      :message="form.errors.insurance_type"
                      class="mt-2"
                    />
                  </div>

                  <!-- Comision -->
                  <div>
                    <!-- Etiqueta -->
                    <custom-label
                      for="building-insurance-commission"
                      class="mb-2"
                      value="Comisión [%]"
                      :class="{
                        'text-opacity-50': !form.insured,
                      }"
                      :required="!form.insured"
                    />
                    <!-- Input -->
                    <jet-input
                      id="building-insurance-commission"
                      name="building-insurance-commission"
                      type="number"
                      v-model.number="form.insurance_commission"
                      class="w-full text-center text-gray-800"
                      :class="{
                        'text-opacity-50': !form.insured,
                      }"
                      :disabled="!form.insured"
                      placeholder="0%"
                      min="0"
                      max="100"
                    />

                    <!-- Error -->
                    <jet-input-error
                      :message="form.errors.insurance_commission"
                      class="mt-2"
                    />
                  </div>
                </div>
              </div>

              <!-- Codigo del inmueble -->
              <div class="col-span-2 lg:col-span-1">
                <div class="grid grid-cols-3 gap-2">
                  <!-- Codigo -->
                  <div>
                    <!-- Etiqueta -->
                    <custom-label
                      for="building-code"
                      class="mb-2"
                      value="Codigo"
                    />
                    <!-- Input -->
                    <jet-input
                      id="building-code"
                      name="building-code"
                      type="number"
                      v-model="form.code"
                      class="w-full text-center text-sm"
                      placeholder="0%"
                      min="0"
                    />

                    <!-- Error -->
                    <jet-input-error :message="form.errors.code" class="mt-2" />
                  </div>
                </div>
              </div>

              <!-- Checkers -->
              <div class="col-span-2">
                <div class="flex">
                  <!-- Has mandate contract -->
                  <label class="flex items-center mr-3">
                    <jet-checkbox
                      name="mandateContract"
                      v-model:checked="form.mandate_contract"
                    />
                    <span class="ml-2 text-sm text-gray-600"
                      >Tiene contrato de mandato</span
                    >
                  </label>

                  <!-- Available -->
                  <label class="flex items-center mr-3">
                    <jet-checkbox
                      name="available"
                      v-model:checked="form.available"
                    />
                    <span class="ml-2 text-sm text-gray-600"
                      >Inmueble disponible</span
                    >
                  </label>
                </div>
              </div>
            </div>
          </input-group-section>
        </template>

        <template #actions>
          <jet-action-message :on="form.recentlySuccessful" class="mr-3">
            Guardado.
          </jet-action-message>

          <label class="flex items-center mr-3">
            <jet-checkbox name="remember" v-model:checked="form.addOther" />
            <span class="ml-2 text-sm text-gray-600">Agregar Otro</span>
          </label>

          <jet-button
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Guardar
          </jet-button>
        </template>
      </jet-form-section>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import LinkButton from "@/Components/Form/LinkButton.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import CustomLabel from "@/Components/Form/Label.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import { useForm } from "@inertiajs/inertia-vue3";
import TrashIcon from "@/Components/Svgs/Trash.vue";
import InputGroupSection from "@/Components/Form/InputGroupSection.vue";
import CurrencyInput from "@/Components/CurrencyInput.vue";
import Swal from "sweetalert2";
import SumaryInfo from "./Partial/SumaryInfo.vue";

import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import locale_es_do from "dayjs/locale/es";
import localizedFormat from "dayjs/plugin/localizedFormat";

export default {
  components: {
    AppLayout,
    JetButton,
    JetSecondaryButton,
    LinkButton,
    JetFormSection,
    JetInput,
    JetInputError,
    CustomLabel,
    JetActionMessage,
    JetCheckbox,
    TrashIcon,
    InputGroupSection,
    CurrencyInput,
    SumaryInfo,
  },
  props: {
    departments: {
      type: Array,
      default: [],
    },
    customers: {
      type: Array,
      default: [],
    },
    admins: {
      type: Array,
      default: [],
    },
    allDistricts: {
      type: Array,
      default: [],
    },
    code: Number,
  },
  setup(props) {
    const form = useForm({
      owner_id: null,
      building_admin_id: null,
      country_department_id: null,
      town_id: null,
      town_district_id: null,
      code: props.code,
      image: null,
      description: null,
      features: null,
      address: null,
      building_type: null,
      socioeconomic: null,
      rooms: null,
      bathrooms: null,
      parking_lots: null,
      area: null,
      private_area: null,
      floor: null,
      antiquity: null,
      lease_fee: 0,
      admin_fee: 0,
      commission: 0,
      insured: false,
      insurance_carrier: null,
      insurance_type: null,
      insurance_commission: 0,
      available: false,
      mandate_contract: false,
      addOther: false,
    });

    //----------------------------------------------------
    // SE ESTABLECEN LOS PARAMETROS DE dayjs
    //----------------------------------------------------
    dayjs.locale(locale_es_do);
    dayjs.extend(relativeTime);
    dayjs.extend(localizedFormat);
    return { form };
  },
  data() {
    return {
      imagePreview: null,
      feature: null,
      features: [],
      address: {
        department: null,
        town: null,
        district: null,
        address: null,
        apartment: null,
        observation: null,
      },
      showGeneralForm: true,
      showAddressForm: false,
      ashoStateForm: false,
    };
  },
  methods: {
    submit() {
      let url = route("building.store");
      this.prepareForm();
      this.form.post(url, {
        onSuccess: (page) => {
          let res = page.props.flash.message;
          if (res.ok && res.reload) {
            this.form.reset();
            this.form.addOther = true;
            this.removeImagePreview();
            this.$refs.description?.focus();
            if (res.building.address) {
              this.resetAddress(res.building.address);
            }
            this.showConfirm(res.building);
          }
        },
        onError: (errors) => {
          console.log(errors);
        },
      });
    },
    /**
     * Se encarga de montar la imagen al formulario
     * y de actualizar la previsualización del archivo en pantalla.
     */
    updateImagePreview() {
      const newImage = this.$refs.image.files[0];
      if (!newImage) return;

      this.form.image = newImage;
      const reader = new FileReader();
      reader.onload = (e) => {
        this.imagePreview = e.target.result;
      };

      reader.readAsDataURL(newImage);
    },
    /**
     * Retira el archivo tanto del campo como del formulario
     * y resetea la propiedad de la imagen.
     */
    removeImagePreview() {
      if (this.imagePreview && this.$refs.image) {
        this.$refs.image.value = null;
        this.imagePreview = null;
        this.form.image = null;
      }
    },
    /**
     * Agrega la caracteristica escrita en el campo
     * al listado.
     */
    addFeature() {
      console.log("Se lanzó");
      if (this.feature && typeof this.feature === "string") {
        let text = this.feature.toLowerCase();
        text = text.charAt(0).toUpperCase() + text.slice(1);
        this.features.push(text);
        this.feature = null;
      }
    },
    /**
     * Retira la caracteristica del listado de caracteristicas
     * @param {Number} index
     */
    removeFeature(index) {
      this.features.splice(index, 1);
    },
    /**
     * Crea un objeto para la dirección simplificado. Elimina los pueblos en
     * el departamento seleccionado y los barrios en el municipio. Ademas solo
     * admite el id y el nombre de cada uno.
     * @return {null|object}
     */
    getAddress() {
      if (
        this.department ||
        this.town ||
        this.district ||
        this.address.address ||
        this.address.observation
      ) {
        return {
          department: {
            id: this.department?.id,
            name: this.department?.name,
          },
          town: {
            id: this.town?.id,
            name: this.town?.name,
          },
          district: {
            id: this.district?.id,
            name: this.district?.name,
          },
          address: this.address.address,
          observation: this.address.observation,
          apartment: this.additionalAddress ? this.address.apartment : null,
        };
      }

      return null;
    },
    /**
     * Se encarga de agregar los campos faltantes al formulario.
     */
    prepareForm() {
      //Se agrega el arreglo de la direción
      this.form.address = this.getAddress();
      //Se agregan las caracteristicas
      this.form.features = this.features.length > 0 ? this.features : null;

      //Se setea admin fee y lease fee
      if (!this.form.lease_fee) this.form.lease_fee = 0;
      if (!this.form.admin_fee || !this.form.building_admin_id)
        this.form.admin_fee = 0;

      //Se resetean los campos del seguro
      if (this.form.insured) {
        this.form.insurance_carrier = null;
        this.form.insurance_type = null;
        this.insurance_commission = 0;
      }
    },
    /**
     * Se encarga de volver a setear los campos de departamento,
     * minicipio y barrio del formulario
     */
    resetAddress(address) {
      //Se limpian las variables
      this.address.address = null;
      this.address.observation = null;
      this.address.department = null;
      this.form.country_department_id = address ? address.department.id : null;
      setTimeout(() => {
        this.form.town_id = address ? address.town.id : null;
        setTimeout(() => {
          this.form.town_district_id = address ? address.district.id : null;
        }, 50);
      }, 50);
    },
    showConfirm(building) {
      let address = building.address;
      let message = "Se guardó el inmueble con ubicación: ";
      message += `${address.district.name} ${address.address} ${
        address.observation ? address.observation : ""
      }.`;

      Swal.fire({
        title: `¡Inmueble Registrado!`,
        icon: building.image_url ? null : "success",
        imageUrl: building.image_url,
        imageWidth: 200,
        text: message,
      });
    },
  }, //.end method
  computed: {
    department() {
      if (this.form.country_department_id) {
        return this.departments.find(
          (item) => item.id === this.form.country_department_id
        );
      }

      return null;
    },
    towns() {
      if (this.department) {
        return this.department.towns;
      }

      return [];
    },
    town() {
      if (this.form.town_id) {
        return this.towns.find((item) => item.id === this.form.town_id);
      }

      return null;
    },
    districts() {
      if (this.town) {
        return this.town.districts;
      }

      return this.allDistricts;
    },
    district() {
      if (this.form.town_district_id) {
        return this.districts.find(
          (dis) => dis.id === this.form.town_district_id
        );
      }

      return null;
    },
    additionalAddress() {
      return (
        this.form.building_type === "apartment" ||
        this.form.building_type === "business"
      );
    },
    maxDate() {
      return dayjs().format("YYYY-MM-DD");
    },
    antiquity() {
      if (this.form.antiquity) {
        return dayjs(this.form.antiquity).fromNow(true);
      }

      return null;
    },
    /**
     * Define si el inmueble tiene administración
     */
    hasAdmin() {
      return this.form.building_admin_id ? true : false;
    },
    /**
     * Recupera la instancia del propietario seleccionada
     */
    owner() {
      if (this.form.owner_id) {
        return this.customers.find((item) => item.id === this.form.owner_id);
      }

      return null;
    },
  }, //.end computed
  watch: {
    "form.country_department_id"(newValue) {
      //Se define si el pueblo está en el listado del departamento.
      let departmentHasTown = this.towns.some(
        (item) => item.id === this.form.town_id
      );

      /**
       * Si el departamento es null, el municipio es null.
       * Si el departamento no es null y el municipio no está en el listado
       * entonces el municipio es null.
       */
      if (!newValue || (!departmentHasTown && newValue)) {
        this.form.town_id = null;
      }
    },
    "form.town_id"(newValue) {
      let townHasDistrict = this.districts.some(
        (item) => item.id === this.form.town_district_id
      );

      /**
       * Se define el si se resetea el valor del distrito
       * Si el municipio no tiene valor, el distrito es null
       * Si el municipio tiene valor pero no esta el distrito, entonces es null
       */
      if (!newValue || (!townHasDistrict && newValue)) {
        this.form.town_district_id = null;
      }
    },
    "form.town_district_id"(newValue) {
      //Se actualizan los ID del departamento y del municipio si estas no tienen asignado uno
      if (
        (!this.form.town_id || !this.form.country_department_id) &&
        newValue
      ) {
        //Se busca la instancia del barrio en cuestion
        let district = this.allDistricts.find((item) => item.id === newValue);
        this.form.country_department_id = district.town.country_department_id;
        setTimeout(() => {
          this.form.town_id = district.town.id;
        }, 50);
      }
    },
    "form.building_admin_id"(newValue, oldValue) {
      //Se recupera las instancias de las adiministraciones y la dirección.
      let newAdmin = this.admins.find((item) => item.id == newValue);
      let oldAdmin = this.admins.find((item) => item.id == oldValue);
      let address = this.address.address;

      //Se la instancia de la administración tiene valor
      if (newAdmin) {
        //Se modifica los campos si la dirección es cero o si la que tiene es
        //la misma que la de la adminsitración anterior.
        if (!address || (oldAdmin && address == oldAdmin.address)) {
          //Se establece la dirección igual a la de la adminsitración
          this.address.address = newAdmin.address;

          //Se establece lel indicador del departamento
          if (newAdmin.department)
            this.form.country_department_id = newAdmin.department.id;

          //Se establece el indicador para el pueblo
          if (newAdmin.town) {
            setTimeout(() => {
              this.form.town_id = newAdmin.town.id;
            }, 50);
          }

          //Se establece el indicador para el barrio
          if (newAdmin.district) {
            setTimeout(() => {
              this.form.town_district_id = newAdmin.district.id;
            }, 100);
          }
        }
      } else if (oldAdmin && address && address == oldAdmin.address) {
        //Si no tiene valor entoneces se resetean los cmapos solo si la direcció
        //es la misma que la de la vieja dministración.
        this.address.address = null;
        this.form.country_department_id = null;
      }
    },
  },
};
</script>