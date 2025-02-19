<template>
    <div>
        <!-- CSRF Token -->
        <Head>
            <meta name="csrf-token" :content="$page.props.csrf_token">
        </Head>
        <!-- rest of your layout -->
    </div>
</template> 